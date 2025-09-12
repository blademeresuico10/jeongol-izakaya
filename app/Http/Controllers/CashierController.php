<?php

namespace App\Http\Controllers;

use App\Models\customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\reservation;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;
use App\Models\menu;

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
            $baseName = $item->menu_item;

            $menuPricesMap[$baseName] = [
                'regular' => $item->regular_price,
                'student' => $item->student_price,
                'govt_employee' => $item->govt_employee_price,
                'has_discount' => $item->has_customer_discount
            ];
        }

        $groupedMenu = [];
        foreach ($menuItems as $item) {
            $groupedMenu[$item->category][] = $item;
        }
        $menuData = menu::select([
            'menu_item',
            'regular_price',
            'student_price',
            'govt_employee_price',
            'has_customer_discount'
        ])->get();

        return view('cashier.home', compact(
            'tables',
            'menuItems',
            'reservations',
            'menuPricesMap',
            'groupedMenu',
            'occupiedTables',
            'menuData'
        ));
    }

    public function getOrders($reservationId)
    {
        $reservation = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->where('reservations.id', $reservationId)
            ->select(
                'reservations.id as reservation_id',
                'reservations.reservation_time',
                'customers.name as customer_name',
                'customers.contact_number',
                'customers.id_type',
                'reservations.pax',
                'reservations.customer_id'
            )
            ->first();

        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $orders = DB::table('order_details')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->where('order_details.reservation_id', $reservationId)
            ->select(
                'order_details.id as order_detail_id',
                'order_details.order_price',
                'order_details.quantity',
                'menu.menu_item as order_name',
                'menu.regular_price',
                'menu.has_customer_discount'
            )
            ->get()
            ->map(function ($order) {
                if (!$order->order_price || !$order->quantity) {
                    return null;
                }

                $order->unit_price = $order->order_price / $order->quantity;
                return $order;
            })
            ->filter();

        return response()->json([
            'reservation_id'   => $reservation->reservation_id,
            'customer_name'    => $reservation->customer_name,
            'contact_number'   => $reservation->contact_number,
            'id_type'          => $reservation->id_type,
            'customer_id'      => $reservation->customer_id,
            'pax'              => $reservation->pax,
            'reservation_time' => $reservation->reservation_time,
            'orders'           => $orders
        ]);
    }


    private function calculateMenuPrice($menuItem, $customerType = null)
    {
        if (!$menuItem->has_customer_discount) {
            return $menuItem->regular_price;
        }

        switch ($customerType) {
            case 'student':
                return $menuItem->student_price ?? $menuItem->regular_price;
            case 'government':
            case 'govt_employee':
                return $menuItem->govt_employee_price ?? $menuItem->regular_price;
            case 'pwd_senior':
                return $menuItem->regular_price * 0.8;
            default:
                return $menuItem->regular_price;
        }
    }

    public function processPayment(Request $request)
    {
        Log::info('Payment processing started', [
            'reservation_id' => $request->reservation_id,
            'customer_data_count' => $request->has('customer_data') ? count($request->customer_data) : 0,
            'orders_count' => $request->has('orders') ? count($request->orders) : 0
        ]);

        $request->validate([
            'reservation_id' => 'required|integer|exists:reservations,id',
            'total' => 'required|numeric|min:0',
            'orders' => 'required|array|min:1',
            'orders.*.order_detail_id' => 'required|integer',
            'orders.*.order_name' => 'required|string',
            'orders.*.quantity' => 'required|integer|min:1',
            'orders.*.price' => 'required|numeric|min:0',
            'customer_data' => 'nullable|array',
            'customer_data.*.name' => 'required_with:customer_data|string',
            'customer_data.*.id_type' => 'nullable|string',
            'customer_data.*.customer_type' => 'nullable|string|in:student,govt_employee,pwd_senior,regular',
            'customer_data.*.item_index' => 'required_with:customer_data|integer',
            'discounted_persons' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $reservation = DB::table('reservations')
                ->join('customers', 'reservations.customer_id', '=', 'customers.id')
                ->where('reservations.id', $request->reservation_id)
                ->select('reservations.id as reservation_id', 'reservations.customer_id')
                ->first();

            if (!$reservation) {
                throw new Exception('Reservation not found');
            }

            $customerMap = [];
            if ($request->has('customer_data')) {
                foreach ($request->customer_data as $customerInfo) {
                    Log::info('Creating customer with data:', $customerInfo);

                    $customer = customers::create([
                        'name' => $customerInfo['name'],
                        'id_type' => $customerInfo['id_type'] ?? null,
                    ]);

                    Log::info('Customer created:', [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'id_type' => $customer->id_type
                    ]);

                    $customerMap[$customerInfo['item_index']] = [
                        'customer' => $customer,
                        'customer_type' => $customerInfo['customer_type'] ?? 'regular'
                    ];
                }
            }

            $mainCustomer = customers::find($reservation->customer_id) ??
                customers::create([
                    'name' => 'Walk-in Customer',
                    'id_type' => null,
                ]);

            DB::table('reservations')->where('id', $request->reservation_id)
                ->update(['customer_id' => $mainCustomer->id, 'updated_at' => now()]);

            $subtotal = 0; 
            $totalDiscountAmount = 0;
            $processedOrders = [];
            $itemIndex = 0;

            foreach ($request->orders as $orderData) {
                $orderDetail = DB::table('order_details')->find($orderData['order_detail_id']);
                if (!$orderDetail) {
                    throw new Exception("Order detail not found: {$orderData['order_detail_id']}");
                }

                $menuItem = DB::table('menu')->find($orderDetail->menu_id);
                if (!$menuItem) {
                    throw new Exception("Menu item not found for order detail: {$orderData['order_detail_id']}");
                }

                for ($i = 0; $i < $orderData['quantity']; $i++) {
                    $linkedCustomer = $mainCustomer;
                    $customerType = 'regular';

                    if (isset($customerMap[$itemIndex])) {
                        $linkedCustomer = $customerMap[$itemIndex]['customer'];
                        $customerType = $customerMap[$itemIndex]['customer_type'];
                    }

                    $regularPrice = $menuItem->regular_price;
                    $discountedPrice = $this->calculateMenuPrice($menuItem, $customerType);
                    $discount = $regularPrice - $discountedPrice;

                    $subtotal += $regularPrice;
                    $totalDiscountAmount += $discount;

                    $processedOrders[] = [
                        'order_detail_id' => $orderData['order_detail_id'],
                        'item_name' => $orderData['order_name'],
                        'quantity' => 1,
                        'discount_amount' => $discount,
                        'customer_id' => $linkedCustomer->id,
                    ];

                    $itemIndex++;
                }
            }

            $finalTotal = $subtotal - $totalDiscountAmount;

            $transactionNo = date('Ymd') . '-' . str_pad(
                DB::table('transactions')->whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $transactionId = DB::table('transactions')->insertGetId([
                'transaction_no' => $transactionNo,
                'subtotal' => $subtotal, 
                'discount_total' => $totalDiscountAmount, 
                'total' => $finalTotal, 
                'payment_method' => 'Cash',
                'status' => 'Completed',
                'reservation_id' => $request->reservation_id,
                'customer_id' => $mainCustomer->id,
                'cashier_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($processedOrders as $order) {
                DB::table('transaction_details')->insert([
                    'transaction_id' => $transactionId,
                    'order_detail_id' => $order['order_detail_id'],
                    'customer_id' => $order['customer_id'],
                    'item_name' => $order['item_name'],
                    'quantity' => $order['quantity'],
                    'discount_amount' => $order['discount_amount'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::table('reservations')->where('id', $request->reservation_id)
                ->update(['status' => 'Completed', 'updated_at' => now()]);

            DB::table('order_details')->where('reservation_id', $request->reservation_id)
                ->where('status', 'Pending')
                ->update(['status' => 'Served', 'updated_at' => now()]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'transaction_no' => $transactionNo,
                'transaction_id' => $transactionId,
                'subtotal' => $subtotal,
                'discount_total' => $totalDiscountAmount,
                'total' => $finalTotal,
                'processed_items' => count($processedOrders),
                'discounted_customers' => count($customerMap)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkCustomer($idNumber)
    {
        try {
            $exists = \App\Models\customers::where('id_type', $idNumber)->exists();
            return response()->json(['exists' => $exists]);
        } catch (Exception $e) {
            Log::error('Error checking customer: ' . $e->getMessage());
            return response()->json(['exists' => false, 'error' => 'Unable to check customer']);
        }
    }

    public function getTransactionDetails($transactionId)
    {
        try {
            $transaction = DB::table('transactions')
                ->join('customers', 'transactions.customer_id', '=', 'customers.id')
                ->where('transactions.id', $transactionId)
                ->select('transactions.*', 'customers.name as customer_name', 'customers.id_number')
                ->first();

            $details = DB::table('transaction_details as td')
                ->leftJoin('customers as c', 'td.customer_id', '=', 'c.id')
                ->where('td.transaction_id', $transactionId)
                ->select('td.*', 'c.name as discount_customer_name', 'c.id_number as discount_customer_id')
                ->get();

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
                'details' => $details
            ]);
        } catch (Exception $e) {
            Log::error('Error fetching transaction details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch transaction details'
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
            ->orderBy('created_at', 'asc')
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
