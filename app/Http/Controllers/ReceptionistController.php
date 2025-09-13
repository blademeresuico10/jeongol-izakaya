<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\reservation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\OrderDetail;
use App\Models\menu;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReceptionistController extends Controller
{

    public function home()
    {
        $tables = DB::table('tables')->get();
        $reservations = DB::table('reservations')
            ->whereDate('reservation_time', Carbon::now()->toDateString())
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
            'groupedMenu'
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
                'notes'              => 'nullable|string',
                'payment_method'     => 'nullable|string|in:Cash,GCash,Maya',
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

            // ✅ Conflict check that ignores completed reservations
            $conflict = DB::table('reservations')
                ->where('table_id', $validated['table_id'])
                ->where(function ($query) use ($reservedDateTime, $endDateTime) {
                    $query->where('reservation_time', '<', $endDateTime)
                        ->where('reservation_end_time', '>', $reservedDateTime);
                })
                ->whereNotIn('id', function ($q) {
                    $q->select('reservation_id')
                        ->from('transactions')
                        ->where('status', 'completed');
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
                'advance_payment'      => $validated['advance_payment'] ?? 0.00,
                'reservation_time'     => $reservedDateTime,
                'reservation_end_time' => $endDateTime,
                'table_id'             => $validated['table_id'],
                'table_number'         => $table->table_number,
                'notes'                => $validated['notes'] ?? null,
                'customer_id'          => $customerId,
                'user_id'              => $userId,
                'total_price'          => $totalPrice,
                'status'               => 'Accepted',
            ]);

            if (isset($validated['payment_method']) && $validated['advance_payment'] > 0) {
                DB::table('reservation_payments')->insert([
                    'reservation_id'  => $reservation->id,
                    'registered_name' => $validated['customer_name'],
                    'number'         => $validated['contact_number'],
                    'amount'         => $validated['advance_payment'],
                    'method'         => $validated['payment_method'],
                    'ref_no'         => null,
                    'proof_path'     => null,
                    'status'         => 'Paid',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            foreach ($validated['orders'] ?? [] as $order) {
                $menu = DB::table('menu')->find($order['menu_id']);
                if (!$menu) continue;

                $orderPrice = $menu->regular_price;
                DB::table('order_details')->insert([
                    'order_price'    => $orderPrice * $order['quantity'],
                    'reservation_id' => $reservation->id,
                    'menu_id'        => $menu->id,
                    'quantity'       => $order['quantity'],
                    'notes'          => $order['notes'] ?? null,
                    'status'         => 'Pending',
                    'customer_id'    => $customerId,
                    'user_id'        => $userId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation failed.',
                'error'   => $e->getMessage()
            ]);
        }
    }


    public function reservations(Request $request)
    {
        $date = $request->query('date', 'today');
        $targetDate = Carbon::today('Asia/Manila')->toDateString();

        $reservations = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('order_details', 'reservations.id', '=', 'order_details.reservation_id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->leftJoin('menu', 'order_details.menu_id', '=', 'menu.id')
            ->select(
                'reservations.id as reservation_id',
                'tables.table_number',
                'reservations.pax',
                'reservations.reservation_time',
                'customers.name as customer_name',
                'menu.menu_item',
                'order_details.quantity',
                'order_details.notes',
                'reservations.status'
            )
            ->whereDate('reservations.reservation_time', $targetDate,)
            ->orderBy('reservations.reservation_time', 'desc')
            ->get();

        $completedTransactionReservationIds = DB::table('transactions')
            ->whereIn('reservation_id', $reservations->pluck('reservation_id'))
            ->pluck('reservation_id')
            ->unique();

        $servedTransactions = collect($completedTransactionReservationIds);

        return view('receptionist.view_reservation', compact('reservations', 'servedTransactions'));
    }

    public function modifyOrders()
    {
        $targetDate = Carbon::today('Asia/Manila')->toDateString();
        $menuItems = DB::table('menu')->select('menu_item', 'regular_price as price')->get();

        // First, get reservations that don't have completed transactions
        $validReservations = DB::table('reservations')
            ->leftJoin('transactions', 'transactions.reservation_id', '=', 'reservations.id')
            ->whereDate('reservations.reservation_time', $targetDate)
            ->where('reservations.status', 'Accepted')
            ->where(function ($query) {
                $query->whereNull('transactions.id')
                    ->orWhere('transactions.status', '!=', 'Completed');
            })
            ->pluck('reservations.id');

        // Then get order details for those valid reservations
        $order_details = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('order_details', function ($join) {
                $join->on('order_details.reservation_id', '=', 'reservations.id')
                    ->where(function ($query) {
                        $query->whereNull('order_details.status')
                            ->orWhere('order_details.status', '!=', 'Cancelled');
                    });
            })
            ->leftJoin('menu', 'menu.id', '=', 'order_details.menu_id')
            ->leftJoin('tables', 'tables.id', '=', 'reservations.table_id')
            ->leftJoin('transactions', 'transactions.reservation_id', '=', 'reservations.id')
            ->select(
                'reservations.id as reservation_id',
                'tables.table_number',
                'reservations.pax',
                'reservations.reservation_time',
                'reservations.status',
                'customers.name as customer_name',
                'order_details.id as order_id',
                'order_details.quantity',
                'order_details.notes as order_notes',
                'order_details.status as order_status',
                'menu.menu_item',
                'transactions.status as transaction_status'
            )
            ->whereIn('reservations.id', $validReservations)
            ->orderBy('reservations.reservation_time')
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

            $existingOrders = OrderDetail::where('reservation_id', $reservation->id)
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

            $customerId = $reservation->customer_id;
            $userId = Auth::id();
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
                        $updateData['previous_quantity'] = $existingOrder->quantity;
                        $updateData['quantity'] = $newQuantity;
                        $updateData['order_price'] = $newPrice;
                        $updateData['change_type'] = $quantityDiff > 0 ? 'addition' : 'reduction';
                        $updateData['change_timestamp'] = $currentTime;
                        $needsUpdate = true;
                    } else if (abs($existingOrder->order_price - $newPrice) > 0.01) {
                        $updateData['previous_price'] = $existingOrder->order_price;
                        $updateData['order_price'] = $newPrice;
                        $updateData['change_type'] = 'modification';
                        $updateData['change_timestamp'] = $currentTime;
                        $needsUpdate = true;
                    }

                    if ($existingOrder->notes != $newNotes) {
                        $updateData['notes'] = $newNotes;
                        if (!$needsUpdate) {
                            $updateData['change_type'] = 'modification';
                            $updateData['change_timestamp'] = $currentTime;
                        }
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

                    OrderDetail::create([
                        'reservation_id' => $reservation->id,
                        'menu_id'        => $menuId,
                        'quantity'       => $newQuantity,
                        'order_price'    => $newPrice,
                        'notes'          => $newNotes,
                        'customer_id'    => $customerId,
                        'user_id'        => $userId,
                        'is_added_order' => true,
                        'change_type'    => 'addition',
                        'previous_quantity' => 0,
                        'change_timestamp' => $currentTime,
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
                            'change_type' => 'removal',
                            'change_timestamp' => $currentTime,
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
            Log::error('UpdateOrder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Update failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function viewKitchen(Request $request)
    {

        $stock = DB::table('stock')->get();
        $today = Carbon::today()->toDateString();

        $reservations = DB::table('order_details')
            ->join('customers', 'order_details.customer_id', '=', 'customers.id')
            ->join('reservations', 'order_details.reservation_id', '=', 'reservations.id')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->select(
                'order_details.id as order_id',
                'order_details.quantity',
                'order_details.notes as order_notes',
                'customers.name as customer_name',
                'reservations.id as reservation_id',
                'reservations.table_number',
                'reservations.pax',
                'reservations.reservation_time',
                'menu.menu_item'
            )
            ->whereDate('reservations.reservation_time', $today)
            ->where('reservations.status', 'Accepted')
            ->orderBy('reservations.reservation_time')
            ->get();
        return view('receptionist.view_kitchen', compact('stock', 'reservations'));
    }

    public function acceptReservation(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = 'Accepted';
        $reservation->save();

        if ($reservation->payment) {
            $reservation->payment->status = 'Paid';
            $reservation->payment->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'       => true,
                'status'        => $reservation->status,
                'reservationId' => $reservation->id,
                'unread_count'  => DB::table('notifications')
                    ->where('notifiable_id', Auth::id())
                    ->where('notifiable_type', 'App\\Models\\User')
                    ->whereNull('read_at')
                    ->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Reservation accepted successfully.');
    }

    public function cancelReservation(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = 'Rejected';
        $reservation->save();

        if ($reservation->payment) {
            $reservation->payment->status = 'Rejected';
            $reservation->payment->save();
        }

        $order_details = OrderDetail::where('reservation_id', $reservation->id)->get();
        if ($order_details->count() > 0) {
            foreach ($order_details as $order) {
                $order->status = 'Cancelled';
                $order->save();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'       => true,
                'status'        => $reservation->status,
                'reservationId' => $reservation->id,
                'unread_count'  => DB::table('notifications')
                    ->where('notifiable_id', Auth::id())
                    ->where('notifiable_type', 'App\\Models\\User')
                    ->whereNull('read_at')
                    ->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Reservation cancelled successfully.');
    }

    public function showPayment($id)
    {
        try {
            $reservation = Reservation::with('payment')->findOrFail($id);

            return response()->json([
                'table_id' => $reservation->table_id,
                'advance_payment' => $reservation->advance_payment,
                'payment' => $reservation->payment,
                'pax' => $reservation->pax,
                'reservation' => [
                    'status' => $reservation->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $allNotifications = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->get();

        $notifications = [];
        $unreadCount = 0;

        foreach ($allNotifications as $n) {
            $data = json_decode($n->data, true) ?? [];

            if ($n->read_at === null) {
                $unreadCount++;
            }

            $reservation = \App\Models\reservation::find($data['reservation_id'] ?? null);

            $notifications[] = [
                'id'             => $n->id,
                'reservation_id' => $data['reservation_id'] ?? null,
                'name'           => $data['customer_name']
                    ?? $reservation?->customer?->name
                    ?? 'Unknown',
                'message'        => $data['message'] ?? '',
                'time'           => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                'status'         => $reservation?->status ?? 'Pending',
                'is_read'        => $n->read_at !== null,
            ];
        }

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
}
