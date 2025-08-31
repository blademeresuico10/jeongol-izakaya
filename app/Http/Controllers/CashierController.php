<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

class CashierController extends Controller
{
    public function home()
    {
        $now = Carbon::now();

        $tables = DB::table('tables')->get();
        $menuItems = DB::table('menu')->get();

        $reservations = DB::table('reservations')
            ->leftJoin('transactions', 'reservations.id', '=', 'transactions.reservation_id')
            ->whereDate('reservations.reservation_time', $now->toDateString())
            ->where('reservations.reservation_time', '<=', $now)
            ->where('reservations.reservation_end_time', '>=', $now)
            ->where('reservations.status', 'Accepted')
            ->whereNull('transactions.id') 
            ->select('reservations.*')
            ->get();

        $reservationIds = $reservations->pluck('id')->toArray();
        $orders = DB::table('order_details')
            ->whereIn('reservation_id', $reservationIds)
            ->get()
            ->groupBy('reservation_id');

        $occupiedTables = [];
        foreach ($tables as $table) {
            $res = $reservations->firstWhere('table_id', $table->id);

            if ($res) {
                $table->current_reservation_id = $res->id;

                $endTime = Carbon::parse($res->reservation_end_time);
                $table->remaining_seconds = $endTime->lessThanOrEqualTo($now)
                    ? 0
                    : $now->diffInSeconds($endTime);

                $table->current_orders = $orders[$res->id] ?? [];
                $occupiedTables[] = $table->table_number; 
            } else {
                $table->current_reservation_id = null;
                $table->remaining_seconds = null;
                $table->current_orders = [];
            }
        }

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

        return view('cashier.home', compact(
            'tables',
            'menuItems',
            'reservations',
            'menuPricesMap',
            'groupedMenu',
            'occupiedTables'
        ));
    }

    public function getOrders($reservationId)
    {
        $reservation = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->where('reservations.id', $reservationId)
            ->select(
                'reservations.id as reservation_id',
                'customers.name as customer_name',
                'reservations.pax',
                'reservations.customer_id'
            )
            ->first();

        if (!$reservation) {
            return response()->json(null);
        }

        $orders = DB::table('order_details')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->where('order_details.reservation_id', $reservationId)
            ->select(
                'order_details.id as order_detail_id',
                'menu.menu_item as order_name',
                'order_details.quantity',
                'menu.price as price'
            )
            ->get();

        return response()->json([
            'reservation_id' => $reservation->reservation_id,
            'customer_name' => $reservation->customer_name,
            'customer_id' => $reservation->customer_id,
            'pax' => $reservation->pax,
            'orders' => $orders
        ]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|integer|exists:reservations,id',
            'customer_name' => 'required|string',
            'total' => 'required|numeric|min:0',
            'orders' => 'required|array|min:1',
            'orders.*.order_detail_id' => 'required|integer',
            'orders.*.order_name' => 'required|string',
            'orders.*.quantity' => 'required|integer|min:1',
            'orders.*.price' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $reservation = DB::table('reservations')
                ->join('customers', 'reservations.customer_id', '=', 'customers.id')
                ->where('reservations.id', $request->reservation_id)
                ->select(
                    'reservations.id as reservation_id',
                    'reservations.customer_id',
                    'reservations.table_id',
                    'customers.name as customer_name'
                )
                ->first();

            if (!$reservation) {
                throw new Exception('Reservation not found');
            }

            $subtotal = 0;
            $totalDiscountAmount = 0;
            $processedOrders = [];

            foreach ($request->orders as $orderData) {
                $itemSubtotal = $orderData['price'] * $orderData['quantity'];
                $subtotal += $itemSubtotal;

                $discountedPersons = $request->input("discounted_persons.{$orderData['order_detail_id']}", 0);
                $discountPerPerson = 0;
                $itemDiscountTotal = 0;

                if ($discountedPersons > 0) {
                    $perPersonCost = $itemSubtotal / $orderData['quantity'];
                    $discountPerPerson = $perPersonCost * 0.20;
                    $itemDiscountTotal = $discountPerPerson * $discountedPersons;
                    $totalDiscountAmount += $itemDiscountTotal;
                }

                $processedOrders[] = [
                    'order_detail_id' => $orderData['order_detail_id'],
                    'item_name' => $orderData['order_name'],
                    'quantity' => $orderData['quantity'],
                    'unit_price' => $orderData['price'],
                    'item_subtotal' => $itemSubtotal,
                    'discounted_persons' => $discountedPersons,
                    'discount_per_person' => $discountPerPerson,
                    'item_discount_total' => $itemDiscountTotal,
                    'total_amount' => $itemSubtotal - $itemDiscountTotal
                ];
            }

            $finalTotal = $subtotal - $totalDiscountAmount;

            $transactionNo = 'TXN-' . date('Ymd') . '-' . str_pad(
                DB::table('transactions')->whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $transactionId = DB::table('transactions')->insertGetId([
                'subtotal' => $subtotal,
                'discount_amount' => $totalDiscountAmount,
                'total_amount' => $finalTotal,
                'payment_method' => 'cash',
                'status' => 'completed',
                'reservation_id' => $request->reservation_id,
                'customer_id' => $reservation->customer_id,
                'cashier_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($processedOrders as $order) {
                DB::table('transaction_details')->insert([
                    'transaction_id' => $transactionId,
                    'order_detail_id' => $order['order_detail_id'],
                    'item_name' => $order['item_name'],
                    'quantity' => $order['quantity'],
                    'unit_price' => $order['unit_price'],
                    'item_subtotal' => $order['item_subtotal'],
                    'discounted_persons' => $order['discounted_persons'],
                    'discount_per_person' => $order['discount_per_person'],
                    'item_discount_total' => $order['item_discount_total'],
                    'total_amount' => $order['total_amount'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::table('reservations')
                ->where('id', $request->reservation_id)
                ->update([
                    'status' => 'Accepted',
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'transaction_no' => $transactionNo,
                'transaction_id' => $transactionId,
                'total_amount' => $finalTotal,
                'reservation_status' => 'Accepted' // Include this for frontend reference
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Payment processing failed: ' . $e->getMessage(), [
                'reservation_id' => $request->reservation_id,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTransactionReceipt($transactionId)
    {
        try {
            $transaction = DB::table('transactions')
                ->join('customers', 'transactions.customer_id', '=', 'customers.id')
                ->join('users', 'transactions.cashier_id', '=', 'users.id')
                ->where('transactions.id', $transactionId)
                ->select(
                    'transactions.*',
                    'customers.name as customer_name',
                    'users.name as cashier_name'
                )
                ->first();

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $transactionDetails = DB::table('transaction_details')
                ->where('transaction_id', $transactionId)
                ->get();

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'details' => $transactionDetails
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transaction receipt'
            ], 500);
        }
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
            return response()->json(['success' => true]);
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
            return response()->json(['success' => true]);
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

            $reservation = \App\Models\Reservation::find($data['reservation_id'] ?? null);

            $notifications[] = [
                'id'             => $n->id,
                'reservation_id' => $data['reservation_id'] ?? null,
                'name'           => $n->data['customer_name']
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
