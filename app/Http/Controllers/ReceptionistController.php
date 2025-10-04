<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\reservation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\menu;
use App\Models\Users;
use Illuminate\Support\Facades\Log;
use App\Models\orders;
use App\Models\reservationPayment;
use App\Models\walkin;

class ReceptionistController extends Controller
{

    public function home()
    {
        $currentTime = Carbon::now();

        $tables = \App\Models\table::with(['reservations' => function ($query) use ($currentTime) {
            $query->where('status', 'Active')
                ->where('started_at', '<=', $currentTime)
                ->where('ended_at', '>=', $currentTime);
        }, 'walkin' => function ($query) use ($currentTime) {
            $query->where('status', 'Active')
                ->where('started_at', '<=', $currentTime)
                ->where('ended_at', '>=', $currentTime);
        }])
            ->get()
            ->map(function ($table) {
                $table->is_occupied = $table->reservations->isNotEmpty() || $table->walkin->isNotEmpty();

                $table->reservation_id = $table->reservations->first()->id ?? null;
                $table->reservation_status = $table->reservations->first()->status ?? null;
                $table->reservation_started_at = $table->reservations->first()->started_at ?? null;
                $table->reservation_ended_at = $table->reservations->first()->ended_at ?? null;

                $table->walkin_id = $table->walkin->first()->id ?? null;
                $table->walkin_status = $table->walkin->first()->status ?? null;
                $table->walkin_started_at = $table->walkin->first()->started_at ?? null;
                $table->walkin_ended_at = $table->walkin->first()->ended_at ?? null;

                return $table;
            });

        $reservations = Reservation::whereDate('started_at', Carbon::now()->toDateString())->get();
        $walkin = walkin::whereDate('started_at', Carbon::now()->toDateString())->get();

        $menuItems = menu::all()->map(function ($item) {
            $processedItem = clone $item;
            $processedItem->price = $item->regular_price;
            return $processedItem;
        });

        $groupedMenu = $menuItems->groupBy('category');

        return view('receptionist.home', compact(
            'tables',
            'menuItems',
            'reservations',
            'groupedMenu',
            'walkin',
        ));
    }

    public function storeReservation(Request $request)
    {
        $data = $request->json()->all();
        $data['orders'] = $request->input('orders');

        try {
            $validated = validator($data, [
                'table_id'           => 'required|exists:tables,id',
                'customer_name'      => 'required|string',
                'contact_number'     => 'nullable|string|max:12',
                'pax'                => 'required|integer|min:1',
                'reserved_date'      => 'required|date',
                'arrival_time'       => 'required|date_format:H:i',
                'advance_payment'    => 'nullable|numeric|min:0',
                'orders'             => 'nullable|array',
                'orders.*.menu_id'   => 'required|exists:menu,id',
                'orders.*.quantity'  => 'required|integer|min:1',
                'orders.*.notes'     => 'nullable|string',
            ])->validate();

            $userId = Auth::id();

            $customer = DB::table('customers')
                ->where('name', $validated['customer_name'])
                ->where('contact_number', $validated['contact_number'] ?? '')
                ->first();

            if (!$customer) {
                $customerId = DB::table('customers')->insertGetId([
                    'name'           => $validated['customer_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } else {
                $customerId = $customer->id;
            }

            $reservedDateTime = Carbon::parse($validated['reserved_date'] . ' ' . $validated['arrival_time']);
            $endDateTime = $reservedDateTime->copy()->addHours(2);

            if ($reservedDateTime->toDateString() < now()->toDateString()) {
                return response()->json(['success' => false, 'message' => 'Cannot reserve on a past day.']);
            }

            $reservationConflict = DB::table('reservations')
                ->where('table_id', $validated['table_id'])
                ->whereIn('status', ['Active', 'Pending'])
                ->where(function ($query) use ($reservedDateTime, $endDateTime) {
                    $query->where('started_at', '<', $endDateTime)
                        ->where('ended_at', '>', $reservedDateTime);
                })
                ->exists();

            $walkinConflict = DB::table('walk_ins')
                ->where('table_id', $validated['table_id'])
                ->where('status', 'Active')
                ->where(function ($query) use ($reservedDateTime, $endDateTime) {
                    $query->where('started_at', '<', $endDateTime)
                        ->where('ended_at', '>', $reservedDateTime);
                })
                ->exists();

            if ($reservationConflict || $walkinConflict) {
                return response()->json(['success' => false, 'message' => 'Time slot already taken.']);
            }

            $reservation = Reservation::create([
                'pax'         => $validated['pax'],
                'started_at'  => $reservedDateTime,
                'ended_at'    => $endDateTime,
                'table_id'    => $validated['table_id'],
                'customer_id' => $customerId,
                'user_id'     => $userId,
                'status'      => 'Active',
            ]);

            reservationPayment::create([
                'reservation_id'    => $reservation->id,
                'registered_name'   => null,
                'registered_number' => null,
                'advance_payment'   => $validated['advance_payment'] ?? 0,
                'payment_method'    => 'cash',
                'payment_proof'     => null,
                'ewallet_id'        => null,
            ]);

            foreach ($validated['orders'] ?? [] as $order) {
                $menu = DB::table('menu')->find($order['menu_id']);
                if (!$menu) continue;

                orders::create([
                    'reservation_id' => $reservation->id,
                    'menu_id'        => $menu->id,
                    'quantity'       => $order['quantity'],
                    'price'          => $menu->regular_price,
                    'notes'          => $order['notes'] ?? null,
                    'status'         => 'Pending',
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function storeWalkin(Request $request)
    {
        $data = $request->json()->all();
        $data['orders'] = $request->input('orders');

        try {
            $validated = validator($data, [
                'table_id'           => 'required|exists:tables,id',
                'customer_name'      => 'required|string',
                'contact_number'     => 'nullable|string|max:12',
                'pax'                => 'required|integer|min:1',
                'started_at'         => 'required|date_format:H:i',
                'orders'             => 'nullable|array',
                'orders.*.menu_id'   => 'required|exists:menu,id',
                'orders.*.quantity'  => 'required|integer|min:1',
                'orders.*.notes'     => 'nullable|string',
            ])->validate();

            $userId = Auth::id();

            $customer = DB::table('customers')
                ->where('name', $validated['customer_name'])
                ->first();

            if (!$customer) {
                $customerId = DB::table('customers')->insertGetId([
                    'name' => $validated['customer_name'],
                ]);
            } else {
                $customerId = $customer->id;
            }

            $arrivalDateTime = Carbon::parse(Carbon::now()->toDateString() . ' ' . $validated['started_at']);
            $endDateTime = $arrivalDateTime->copy()->addHours(2);

            $walkinConflict = DB::table('walk_ins')
                ->where('table_id', $validated['table_id'])
                ->where('status', 'Active')
                ->where(function ($query) use ($arrivalDateTime, $endDateTime) {
                    $query->where('started_at', '<', $endDateTime)
                        ->where('ended_at', '>', $arrivalDateTime);
                })
                ->exists();

            $reservationConflict = DB::table('reservations')
                ->where('table_id', $validated['table_id'])
                ->whereIn('status', ['Active', 'Pending'])
                ->where(function ($query) use ($arrivalDateTime, $endDateTime) {
                    $query->where('started_at', '<', $endDateTime)
                        ->where('ended_at', '>', $arrivalDateTime);
                })
                ->exists();

            if ($walkinConflict || $reservationConflict) {
                return response()->json(['success' => false, 'message' => 'Time slot already taken.']);
            }

            $walkin = walkin::create([
                'pax'         => $validated['pax'],
                'started_at'  => $arrivalDateTime,
                'ended_at'    => $endDateTime,
                'table_id'    => $validated['table_id'],
                'customer_id' => $customerId,
                'user_id'     => $userId,
                'status'      => 'Active',
            ]);

            foreach ($validated['orders'] ?? [] as $order) {
                $menu = DB::table('menu')->find($order['menu_id']);
                if (!$menu) continue;

                orders::create([
                    'walk_in_id'     => $walkin->id,
                    'menu_id'        => $menu->id,
                    'quantity'       => $order['quantity'],
                    'price'          => $menu->regular_price,
                    'notes'          => $order['notes'] ?? null,
                    'status'         => 'Pending',
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function bookings(Request $request)
    {
        $targetDate = Carbon::today('Asia/Manila')->toDateString();

        $reservationsQuery = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('orders', 'reservations.id', '=', 'orders.reservation_id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->leftJoin('menu', 'orders.menu_id', '=', 'menu.id')
            ->select(
                'reservations.id as reservation_id',
                'tables.table_number',
                'reservations.pax',
                'reservations.started_at',
                'reservations.ended_at',
                'customers.name as customer_name',
                'menu.menu_item',
                'orders.quantity',
                'orders.notes',
                DB::raw("'reservation' as source"),
                'reservations.status'
            )
            ->whereDate('reservations.started_at', $targetDate);

        $walkInsQuery = DB::table('walk_ins')
            ->join('customers', 'walk_ins.customer_id', '=', 'customers.id')
            ->leftJoin('orders', 'walk_ins.id', '=', 'orders.walk_in_id')
            ->leftJoin('tables', 'walk_ins.table_id', '=', 'tables.id')
            ->leftJoin('menu', 'orders.menu_id', '=', 'menu.id')
            ->select(
                'walk_ins.id as reservation_id',
                'tables.table_number',
                'walk_ins.pax',
                'walk_ins.started_at',
                'walk_ins.ended_at',
                'customers.name as customer_name',
                'menu.menu_item',
                'orders.quantity',
                'orders.notes',
                DB::raw("'walk_in' as source"),
                'walk_ins.status'
            )
            ->whereDate('walk_ins.started_at', $targetDate);

        $combined = $reservationsQuery
            ->unionAll($walkInsQuery)
            ->get()
            ->sortByDesc('started_at')
            ->values();

        $completedTransactionReservationIds = DB::table('transactions')
            ->whereIn('reservation_id', $combined->pluck('reservation_id'))
            ->pluck('reservation_id')
            ->unique();

        $servedTransactions = collect($completedTransactionReservationIds);

        return view('receptionist.view_bookings', [
            'combined' => $combined,
            'servedTransactions' => $servedTransactions,
        ]);
    }

    public function modifyOrders()
    {
        $targetDate = Carbon::today('Asia/Manila')->toDateString();
        $menuItems = DB::table('menu')->select('menu_item', 'regular_price as price')->get();

        $validWalkIns = DB::table('walk_ins')
            ->leftJoin('transactions', 'transactions.walk_in_id', '=', 'walk_ins.id')
            ->whereDate('walk_ins.started_at', $targetDate)
            ->where('walk_ins.status', 'Active')
            ->where(function ($query) {
                $query->whereNull('transactions.id')
                    ->orWhere('transactions.status', '!=', 'Completed');
            })
            ->pluck('walk_ins.id');

        $validReservations = DB::table('reservations')
            ->leftJoin('transactions', 'transactions.reservation_id', '=', 'reservations.id')
            ->whereDate('reservations.started_at', $targetDate)
            ->where('reservations.status', 'Active')
            ->where(function ($query) {
                $query->whereNull('transactions.id')
                    ->orWhere('transactions.status', '!=', 'Completed');
            })
            ->pluck('reservations.id');

        $reservationOrders = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.reservation_id', '=', 'reservations.id')
                    ->where(function ($query) {
                        $query->whereNull('orders.status')
                            ->orWhere('orders.status', '!=', 'Cancelled');
                    });
            })
            ->leftJoin('menu', 'menu.id', '=', 'orders.menu_id')
            ->leftJoin('tables', 'tables.id', '=', 'reservations.table_id')
            ->leftJoin('transactions', 'transactions.reservation_id', '=', 'reservations.id')
            ->select(
                DB::raw("'reservation' as source"),
                'reservations.id as record_id',
                'tables.table_number',
                'reservations.pax',
                'reservations.started_at',
                'reservations.status',
                'customers.name as customer_name',
                'orders.id as order_id',
                'orders.quantity',
                'orders.notes as order_notes',
                'orders.status as order_status',
                'menu.menu_item',
                'transactions.status as transaction_status'
            )
            ->whereIn('reservations.id', $validReservations);

        $walkinOrders = DB::table('walk_ins')
            ->join('customers', 'walk_ins.customer_id', '=', 'customers.id')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.walk_in_id', '=', 'walk_ins.id')
                    ->where(function ($query) {
                        $query->whereNull('orders.status')
                            ->orWhere('orders.status', '!=', 'Cancelled');
                    });
            })
            ->leftJoin('menu', 'menu.id', '=', 'orders.menu_id')
            ->leftJoin('tables', 'tables.id', '=', 'walk_ins.table_id')
            ->leftJoin('transactions', 'transactions.walk_in_id', '=', 'walk_ins.id')
            ->select(
                DB::raw("'walk_in' as source"),
                'walk_ins.id as record_id',
                'tables.table_number',
                'walk_ins.pax',
                'walk_ins.started_at',
                'walk_ins.status',
                'customers.name as customer_name',
                'orders.id as order_id',
                'orders.quantity',
                'orders.notes as order_notes',
                'orders.status as order_status',
                'menu.menu_item',
                'transactions.status as transaction_status'
            )
            ->whereIn('walk_ins.id', $validWalkIns);

        $combinedOrders = $reservationOrders->unionAll($walkinOrders)->get();

        $groupedOrders = $combinedOrders->groupBy('record_id')->map(function ($orders) {
            $orderData = $orders->filter(fn($order) => !is_null($order->menu_item))
                ->map(fn($order) => [
                    'menu_item' => $order->menu_item,
                    'quantity' => (int) $order->quantity
                ])->values()->toArray();

            $ordersWithQty = collect($orderData)
                ->map(fn($o) => "{$o['menu_item']} x {$o['quantity']}")
                ->implode(', ');

            return (object)[
                'source' => $orders->first()->source,
                'reservation_id' => $orders->first()->record_id,
                'record_id' => $orders->first()->record_id,
                'customer_name' => $orders->first()->customer_name,
                'table_number' => $orders->first()->table_number,
                'pax' => $orders->first()->pax,
                'orders' => $ordersWithQty ?: 'No orders',
                'order_data' => json_encode($orderData),
                'note' => $orders->pluck('order_notes')->filter()->unique()->implode(', '),
            ];
        });

        return view('receptionist.modify_orders', compact('groupedOrders', 'menuItems'));
    }


    public function updateOrder(Request $request)
    {
        $request->validate([
            'record_id' => 'required|integer',
            'source' => 'required|in:reservation,walk_in',
            'pax' => 'required|integer|min:1',
            'orders' => 'nullable|json',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $source = $request->source;
            $recordId = $request->record_id;

            // Determine which table and model to use
            if ($source === 'reservation') {
                $record = Reservation::findOrFail($recordId);
                $foreignKey = 'reservation_id';
            } else {
                $record = walkin::findOrFail($recordId);
                $foreignKey = 'walk_in_id';
            }

            // Update pax if changed
            if ($record->pax != $request->pax) {
                $record->pax = $request->pax;
                $record->save();
            }

            // Get existing orders
            $existingOrders = orders::where($foreignKey, $recordId)
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', '!=', 'Cancelled');
                })
                ->get();

            $newOrders = json_decode($request->orders, true) ?: [];

            $existingOrdersMap = $existingOrders->keyBy('menu_id');

            $newOrdersMap = collect($newOrders)->keyBy(function ($item) {
                $menu = Menu::where('menu_item', $item['menu_name'])->first();
                return $menu ? $menu->id : null;
            })->filter(fn($item, $key) => $key !== null);

            $currentTime = now();
            $changes = [];

            // Process new/updated orders
            foreach ($newOrdersMap as $menuId => $newOrder) {
                $menu = Menu::find($menuId);
                if (!$menu) continue;

                $newQuantity = (int) $newOrder['quantity'];
                $unitPrice = $menu->regular_price;
                $newPrice = round($unitPrice * $newQuantity, 2);
                $newNotes = $request->note;

                if ($existingOrdersMap->has($menuId)) {
                    $existingOrder = $existingOrdersMap->get($menuId);
                    $quantityDiff = $newQuantity - $existingOrder->quantity;

                    if ($quantityDiff != 0) {
                        $changeType = $quantityDiff > 0 ? 'addition' : 'reduction';
                        $changes[] = [
                            'type' => $changeType,
                            'menu_name' => str_replace([' Lunch', ' Dinner'], '', $menu->menu_item),
                            'quantity' => abs($quantityDiff),
                            'timestamp' => $currentTime->toISOString()
                        ];
                    }

                    $updateData = [];
                    if ($existingOrder->quantity != $newQuantity) {
                        $updateData['quantity'] = $newQuantity;
                        $updateData['price'] = $newPrice;
                    }
                    if ($existingOrder->notes != $newNotes) {
                        $updateData['notes'] = $newNotes;
                    }
                    if (!empty($updateData)) {
                        $updateData['updated_at'] = $currentTime;
                        $existingOrder->update($updateData);
                    }

                    $existingOrdersMap->forget($menuId);
                } else {
                    // New order
                    $changes[] = [
                        'type' => 'addition',
                        'menu_name' => str_replace([' Lunch', ' Dinner'], '', $menu->menu_item),
                        'quantity' => $newQuantity,
                        'timestamp' => $currentTime->toISOString()
                    ];

                    orders::create([
                        $foreignKey      => $recordId,
                        'menu_id'        => $menuId,
                        'quantity'       => $newQuantity,
                        'price'          => $newPrice,
                        'notes'          => $newNotes,
                        'status'         => 'Pending',
                        'created_at'     => $currentTime,
                        'updated_at'     => $currentTime,
                    ]);
                }
            }

            // Cancel removed orders
            if ($existingOrdersMap->isNotEmpty()) {
                foreach ($existingOrdersMap as $existingOrder) {
                    $menu = Menu::find($existingOrder->menu_id);
                    if ($menu) {
                        $changes[] = [
                            'type' => 'removal',
                            'menu_name' => str_replace([' Lunch', ' Dinner'], '', $menu->menu_item),
                            'quantity' => $existingOrder->quantity,
                            'timestamp' => $currentTime->toISOString()
                        ];
                    }

                    $existingOrder->update([
                        'status' => 'Cancelled',
                        'updated_at' => $currentTime,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($source) . ' updated successfully.',
                'changes' => $changes,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Update failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function acceptReservation(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $reservation = Reservation::findOrFail($id);
            $reservation->status = 'Active';
            $reservation->save();

            if ($reservation->user_id) {
                $this->createNotification(
                    $reservation->user_id,
                    $reservation->id,
                    'The reservation has been accepted and confirmed.'
                );
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => $reservation->status,
                    'reservationId' => $reservation->id,
                    'unread_count' => $this->getUnreadCount(),
                ]);
            }

            return redirect()->back()->with('success', 'Reservation accepted successfully.');
        });
    }

    public function cancelReservation(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $reservation = Reservation::findOrFail($id);
            $reservation->status = 'Rejected';
            $reservation->save();

            orders::where('reservation_id', $reservation->id)
                ->update(['status' => 'Cancelled', 'updated_at' => now()]);

            if ($reservation->user_id) {
                $this->createNotification(
                    $reservation->user_id,
                    $reservation->id,
                    'Your reservation has been cancelled/rejected.'
                );
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => $reservation->status,
                    'reservationId' => $reservation->id,
                    'unread_count' => $this->getUnreadCount(),
                ]);
            }

            return redirect()->back()->with('success', 'Reservation cancelled successfully.');
        });
    }

    public function getNotifications()
    {
        try {
            $notifications = DB::table('reservation_notifications')
                ->join('reservations', 'reservation_notifications.reservation_id', '=', 'reservations.id')
                ->join('customers', 'reservations.customer_id', '=', 'customers.id')
                ->join('tables', 'reservations.table_id', '=', 'tables.id')
                ->leftJoin('reservation_payment_details', 'reservations.id', '=', 'reservation_payment_details.reservation_id')
                ->where('reservation_notifications.user_id', Auth::id())
                ->select([
                    'reservation_notifications.id',
                    'reservation_notifications.message',
                    'reservation_notifications.is_read',
                    'reservation_notifications.created_at',
                    'reservations.id as reservation_id',
                    'reservations.status as reservation_status',
                    'reservations.pax',
                    'reservations.started_at',
                    'reservations.ended_at',
                    'customers.name as customer_name',
                    'tables.table_number',
                    'reservation_payment_details.advance_payment',
                    'reservation_payment_details.payment_proof',
                    'reservation_payment_details.payment_method'
                ])
                ->orderBy('reservation_notifications.created_at', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $this->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications.'
            ], 500);
        }
    }

    public function markNotificationAsRead($id)
    {
        try {
            $updated = DB::table('reservation_notifications')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => $updated ? 'Notification marked as read.' : 'Notification already read or not found.',
                'unread_count' => $this->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification.'
            ], 500);
        }
    }

    public function markAllNotificationsAsRead()
    {
        try {
            $updated = DB::table('reservation_notifications')
                ->where('user_id', Auth::id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "Marked {$updated} notifications as read.",
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update notifications.'
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        try {
            $count = DB::table('reservation_notifications')
                ->where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'unread_count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unread count.'
            ], 500);
        }
    }

    // ADD THESE HELPER METHODS TO ReceptionistController:
    private function createNotification($userId, $reservationId, $message)
    {
        DB::table('reservation_notifications')->insert([
            'user_id' => $userId,
            'reservation_id' => $reservationId,
            'message' => $message,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
