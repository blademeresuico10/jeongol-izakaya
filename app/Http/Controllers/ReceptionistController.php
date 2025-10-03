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


class ReceptionistController extends Controller
{

    public function home()
    {
        $currentTime = Carbon::now();

        $tables = DB::table('tables')
            ->leftJoin('reservations', function ($join) use ($currentTime) {
                $join->on('tables.id', '=', 'reservations.table_id')
                    ->where('reservations.status', '=', 'Active')
                    ->where('reservations.started_at', '<=', $currentTime)
                    ->where('reservations.ended_at', '>=', $currentTime);
            })
            ->leftJoin('walk_ins', function ($join) use ($currentTime) {
                $join->on('tables.id', '=', 'walk_ins.table_id')
                    ->where('walk_ins.status', '=', 'Active')
                    ->where('walk_ins.started_at', '<=', $currentTime)
                    ->where('walk_ins.ended_at', '>=', $currentTime);
            })
            ->select(
                'tables.*',
                'reservations.id as reservation_id',
                'reservations.status as reservation_status',
                'reservations.started_at as reservation_started_at',
                'reservations.ended_at as reservation_ended_at',
                'walk_ins.id as walkin_id',
                'walk_ins.status as walkin_status',
                'walk_ins.started_at as walkin_started_at',
                'walk_ins.ended_at as walkin_ended_at'
            )
            ->get()
            ->map(function ($table) {
                $table->is_occupied = !is_null($table->reservation_id) || !is_null($table->walkin_id);
                return $table;
            });


        $reservations = DB::table('reservations')
            ->whereDate('started_at', Carbon::now()->toDateString())
            ->get();

        $walkins = DB::table('walk_ins')
            ->whereDate('started_at', Carbon::now()->toDateString())
            ->get();

        $menuItems = DB::table('menu')->get()->map(function ($item) {
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
            'walkins',
        ));
    }


    public function storeReservation(Request $request)
    {
        $data = $request->json()->all();
        $data['orders'] = $request->input('orders');

        if (isset($data['payment_method'])) {
            $data['payment_method'] = strtolower($data['payment_method']);
        }

        if (isset($data['payment_method']) && in_array($data['payment_method'], ['gcash', 'maya'])) {
            if (!isset($data['ewallet_number_id']) || empty($data['ewallet_number_id'])) {
                $ewallet = DB::table('ewallet_details')
                    ->where('payment_method', $data['payment_method'])
                    ->where('is_active', true)
                    ->first();

                if ($ewallet) {
                    $data['ewallet_number_id'] = $ewallet->id;
                }
            }
        }

        try {
            $validated = validator($data, [
                'table_id'           => 'required|exists:tables,id',
                'customer_name'      => 'required|string',
                'contact_number'     => 'nullable|string|max:12',
                'pax'                => 'required|integer|min:1',
                'reserved_date'      => 'required|date',
                'arrival_time'       => 'required|date_format:H:i',
                'advance_payment'    => 'nullable|numeric|min:0',
                'notes'              => 'nullable|string',
                'payment_method'     => 'required|string|in:cash,gcash,maya',
                'ewallet_number_id'  => 'required_if:payment_method,gcash,maya|nullable|exists:ewallet_details,id',
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

            $conflict = DB::table('reservations')
                ->where('table_id', $validated['table_id'])
                ->whereIn('status', ['Active', 'Pending']) 
                ->where(function ($query) use ($reservedDateTime, $endDateTime) {
                    $query->where(function ($q) use ($reservedDateTime, $endDateTime) {
                        $q->where('started_at', '<', $endDateTime)
                            ->where('ended_at', '>', $reservedDateTime);
                    });
                })
                ->exists();



            if ($conflict) {
                return response()->json(['success' => false, 'message' => 'Time slot already taken.']);
            }

            $totalPrice = 0;
            foreach ($validated['orders'] ?? [] as $order) {
                $menu = DB::table('menu')->find($order['menu_id']);
                if ($menu) {
                    $price = $menu->regular_price;
                    $totalPrice += $price * $order['quantity'];
                }
            }


            $table = DB::table('tables')->where('id', $validated['table_id'])->first();

            $reservation = Reservation::create([
                'pax'                  => $validated['pax'],
                'started_at'           => $reservedDateTime,
                'ended_at'             => $endDateTime,
                'table_id'             => $validated['table_id'],
                'customer_id'          => $customerId,
                'user_id'              => $userId,
                'status'               => 'Active',
            ]);


            DB::table('reservation_payment_details')->insert([
                'reservation_id'   => $reservation->id,
                'name'             => $validated['customer_name'],
                'contact'          => $validated['contact_number'],
                'advance_payment'  => $validated['advance_payment'] ?? 0,
                'payment_method'   => $validated['payment_method'],
                'payment_proof'    => null,
                'ewallet_number'   => $validated['ewallet_number_id'] ?? null,
                'created_at'       => now(),
                'updated_at'       => now(),
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
            Log::error('Reservation Failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Reservation failed.',
                'error'   => $e->getMessage(),
            ]);
        }
    }

    public function storeWalkIn(Request $request)
    {
        $data = $request->json()->all();
        $data['orders'] = $request->input('orders');

        try {
            $validated = validator($data, [
                'table_id'           => 'required|exists:tables,id',
                'customer_name'      => 'required|string',
                'pax'                => 'required|integer|min:1',
                'notes'              => 'nullable|string',
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
                    'name'           => $validated['customer_name'],
                    'contact_number' => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            } else {
                $customerId = $customer->id;
            }

            $startedAt = now();
            $endedAt   = $startedAt->copy()->addHours(2);

            $reservationConflict = DB::table('reservations')
                ->where('table_id', $validated['table_id'])
                ->where(function ($query) use ($startedAt, $endedAt) {
                    $query->where('started_at', '<', $endedAt)
                        ->where('ended_at', '>', $startedAt);
                })
                ->whereIn('status', ['Active', 'Pending'])
                ->exists();

            if ($reservationConflict) {
                return response()->json(['success' => false, 'message' => 'Table is already reserved at this time.']);
            }

            $walkInConflict = DB::table('walk_ins')
                ->where('table_id', $validated['table_id'])
                ->where(function ($query) use ($startedAt, $endedAt) {
                    $query->where('started_at', '<', $endedAt)
                        ->where('ended_at', '>', $startedAt);
                })
                ->where('status', 'Active')
                ->exists();

            if ($walkInConflict) {
                return response()->json(['success' => false, 'message' => 'Table is already occupied.']);
            }

            $walkIn = DB::table('walk_ins')->insertGetId([
                'customer_id'    => $customerId,
                'table_id'       => $validated['table_id'],
                'user_id'        => $userId,
                'pax'            => $validated['pax'],
                'started_at'     => $startedAt,
                'ended_at'       => $endedAt,
                'status'         => 'Active',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            foreach ($validated['orders'] ?? [] as $order) {
                $menu = DB::table('menu')->find($order['menu_id']);
                if (!$menu) continue;

                DB::table('orders')->insert([
                    'walk_in_id'     => $walkIn,
                    'menu_id'        => $menu->id,
                    'quantity'       => $order['quantity'],
                    'price'          => $menu->regular_price,
                    'notes'          => $order['notes'] ?? null,
                    'status'         => 'Pending',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error!.',
                'error'   => $e->getMessage()
            ]);
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

        $validReservations = DB::table('reservations')
            ->leftJoin('transactions', 'transactions.reservation_id', '=', 'reservations.id')
            ->whereDate('reservations.started_at', $targetDate)
            ->where('reservations.status', 'Active')
            ->where(function ($query) {
                $query->whereNull('transactions.id')
                    ->orWhere('transactions.status', '!=', 'Completed');
            })
            ->pluck('reservations.id');

        $order_details = DB::table('reservations')
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
                'reservations.id as reservation_id',
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
            ->whereIn('reservations.id', $validReservations)
            ->orderBy('reservations.started_at')
            ->get();

        $groupedOrders = $order_details->groupBy('reservation_id')->map(function ($orders, $reservationId) {
            $orderData = $orders->filter(function ($order) {
                return !is_null($order->menu_item);
            })->map(function ($order) {
                return [
                    'menu_item' => $order->menu_item,
                    'quantity' => (int)$order->quantity
                ];
            })->values()->toArray();

            $ordersWithQty = $orders->filter(function ($order) {
                return !is_null($order->menu_item);
            })->map(function ($order) {
                return $order->menu_item . ' x ' . $order->quantity;
            })->implode(', ');

            return (object)[
                'reservation_id' => $reservationId,
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
            'reservation_id' => 'required|exists:reservations,id',
            'pax' => 'required|integer|min:1',
            'orders' => 'nullable|json',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $reservation = Reservation::findOrFail($request->reservation_id);

            if ($reservation->pax != $request->pax) {
                $reservation->pax = $request->pax;
                $reservation->save();
            }

            $existingOrders = orders::where('reservation_id', $reservation->id)
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhere('status', '!=', 'Cancelled');
                })
                ->get();

            $newOrders = json_decode($request->orders, true) ?: [];

            $existingOrdersMap = $existingOrders->keyBy(function ($item) {
                return $item->menu_id;
            });

            $newOrdersMap = collect($newOrders)->keyBy(function ($item) {
                $menu = Menu::where('menu_item', $item['menu_name'])->first();
                return $menu ? $menu->id : null;
            })->filter(function ($item, $key) {
                return $key !== null;
            });

            $currentTime = now();
            $changes = [];

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

                    $needsUpdate = false;
                    $updateData = [];

                    if ($existingOrder->quantity != $newQuantity) {
                        $updateData['quantity'] = $newQuantity;
                        $updateData['price'] = $newPrice;
                        $needsUpdate = true;
                    }

                    if ($existingOrder->notes != $newNotes) {
                        $updateData['notes'] = $newNotes;
                        $needsUpdate = true;
                    }

                    if ($needsUpdate) {
                        $updateData['updated_at'] = $currentTime;
                        $existingOrder->update($updateData);
                    }

                    $existingOrdersMap->forget($menuId);
                } else {
                    $changes[] = [
                        'type' => 'addition',
                        'menu_name' => str_replace([' Lunch', ' Dinner'], '', $menu->menu_item),
                        'quantity' => $newQuantity,
                        'timestamp' => $currentTime->toISOString()
                    ];

                    orders::create([
                        'reservation_id' => $reservation->id,
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

            if ($existingOrdersMap->isNotEmpty()) {
                foreach ($existingOrdersMap as $menuId => $existingOrder) {
                    $menu = Menu::find($menuId);
                    if ($menu) {
                        $changes[] = [
                            'type' => 'removal',
                            'menu_name' => str_replace([' Lunch', ' Dinner'], '', $menu->menu_item),
                            'quantity' => $existingOrder->quantity,
                            'timestamp' => $currentTime->toISOString()
                        ];

                        $existingOrder->update([
                            'status' => 'Cancelled',
                            'updated_at' => $currentTime,
                        ]);
                    }
                }
            }

            $sessionKey = "order_changes_{$reservation->id}";
            $existingChanges = session($sessionKey, []);
            $allChanges = array_merge($existingChanges, $changes);
            $allChanges = array_slice($allChanges, -10);
            session()->put($sessionKey, $allChanges);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation updated successfully.',
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
