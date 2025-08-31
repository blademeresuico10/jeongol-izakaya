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

class ReceptionistController extends Controller
{
    public function home()
    {
        $tables = DB::table('tables')->get();
        $menuItems = DB::table('menu')->get();


        $now = Carbon::now();
        $reservations = DB::table('reservations')
            ->whereDate('reservation_time', $now->toDateString())
            ->get();


        $menuPricesMap = [];
        foreach ($menuItems as $item) {
            $baseName = str_replace([' Lunch', ' Dinner'], '', $item->menu_item);
            if (!isset($menuPricesMap[$baseName])) {
                $menuPricesMap[$baseName] = ['lunch' => null, 'dinner' => null];
            }

            if (str_contains($item->menu_item, 'Lunch')) {
                $menuPricesMap[$baseName]['lunch'] = $item->price;
            } elseif (str_contains($item->menu_item, 'Dinner')) {
                $menuPricesMap[$baseName]['dinner'] = $item->price;
            } else {
                $menuPricesMap[$baseName]['lunch'] = $item->price;
                $menuPricesMap[$baseName]['dinner'] = $item->price;
            }
        }

        $groupedMenu = [];
        foreach ($menuItems as $item) {
            $groupedMenu[$item->category][] = $item;
        }

        return view('receptionist.home', compact('tables', 'menuItems', 'reservations', 'menuPricesMap', 'groupedMenu'));
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
                    'name'         => $validated['customer_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            } else {
                $customerId = $customer->id;
            }

            $reservedDateTime = Carbon::parse($validated['reserved_date'] . ' ' . $validated['arrival_time']);
            $endDateTime = $reservedDateTime->copy()->addHours(2);

            if ($reservedDateTime->toDateString() < now()->toDateString()) {
                return response()->json(['success' => false, 'message' => 'Cannot reserve on a past day.']);
            }

            // FIX: Use table_id instead of table_number
            $conflict = DB::table('reservations')
                ->where('table_id', $validated['table_id'])  // ✅ Changed from table_number to table_id
                ->where(function ($query) use ($reservedDateTime, $endDateTime) {
                    $query->where('reservation_time', '<', $endDateTime)
                        ->where('reservation_end_time', '>', $reservedDateTime);
                })
                ->exists();

            if ($conflict) {
                return response()->json(['success' => false, 'message' => 'Time slot already taken.']);
            }

            $isLunch = $reservedDateTime->format('H') < 17;
            $totalPrice = 0;
            if (!empty($validated['orders'])) {
                foreach ($validated['orders'] as $order) {
                    $menu = DB::table('menu')->find($order['menu_id']);

                    if ($menu) {
                        $totalPrice += $menu->price * $order['quantity'];
                    }
                }
            }

            // Get table info for storing table_number if needed
            $table = DB::table('tables')->where('id', $validated['table_id'])->first();

            $reservation = Reservation::create([
                'pax'                  => $validated['pax'],
                'advance_payment'      => $validated['advance_payment'] ?? 0.00,
                'reservation_time'     => $reservedDateTime,
                'reservation_end_time' => $endDateTime,
                'table_id'             => $validated['table_id'],  // ✅ Use table_id
                'table_number'         => $table->table_number,    // ✅ Store table_number if your model needs it
                'notes'                => $validated['notes'] ?? null,
                'customer_id'          => $customerId,
                'user_id'              => $userId,
                'total_price'          => $totalPrice,
                'status'               => 'Pending',
            ]);

            foreach ($validated['orders'] ?? [] as $order) {
                $menu = DB::table('menu')->find($order['menu_id']);
                if (!$menu) continue;

                DB::table('order_details')->insert([
                    'order_price'    => $menu->price * $order['quantity'],
                    'reservation_id' => $reservation->id,
                    'menu_id'        => $menu->id,
                    'quantity'       => $order['quantity'],
                    'notes'          => $order['notes'] ?? null,
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
            ->whereDate('reservations.reservation_time', $targetDate)
            ->where('reservations.status', 'Accepted')
            ->orderBy('reservations.reservation_time')
            ->get();

        return view('receptionist.view_reservation', compact('reservations'));
    }

    public function modifyOrders()
    {
        $targetDate = Carbon::today('Asia/Manila')->toDateString();
        $menuItems = DB::table('menu')->select('menu_item', 'price')->get();

        $order_details = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('order_details', 'order_details.reservation_id', '=', 'reservations.id')
            ->leftJoin('menu', 'menu.id', '=', 'order_details.menu_id')
            ->leftJoin('tables', 'tables.id', '=', 'reservations.table_id')
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
                'menu.menu_item'
            )
            ->whereDate('reservations.reservation_time', $targetDate)
            ->where('reservations.status', 'Accepted')
            ->orderBy('reservations.reservation_time')
            ->get();


        $groupedOrders = $order_details->groupBy('reservation_id')->map(function ($orders, $reservationId) {
            return (object)[
                'reservation_id' => $reservationId,
                'customer_name' => $orders->first()->customer_name,
                'table_number' => $orders->first()->table_number,
                'pax' => $orders->first()->pax,
                'orders' => $orders->pluck('menu_item')->implode(', '),
                'qty' => $orders->pluck('quantity')->implode(', '),
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
            $reservation->pax = $request->pax;
            $reservation->save();

            $customerId = $reservation->customer_id;
            $userId = Auth::id();

            OrderDetail::where('reservation_id', $reservation->id)->delete();

            $orders = json_decode($request->orders, true);

            foreach ($orders as $order) {
                $menu = Menu::where('menu_item', $order['menu_name'])->first();
                if (!$menu) continue;

                OrderDetail::create([
                    'reservation_id' => $reservation->id,
                    'menu_id'        => $menu->id,
                    'quantity'       => $order['quantity'],
                    'order_price'    => $menu->price * $order['quantity'],
                    'notes'          => $request->note,
                    'customer_id'    => $customerId,
                    'user_id'        => $userId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation updated successfully.'
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
    public function markNotificationRead(Request $request, $id)
    {
        DatabaseNotification::where('data->reservation_id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
    public function showPayment($id)
    {
        try {
            $reservation = Reservation::with('payment')->findOrFail($id);

            return response()->json([
                'advance_payment' => $reservation->advance_payment,
                'payment' => $reservation->payment,
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
