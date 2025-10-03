<?php

namespace App\Http\Controllers;

use App\Models\customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\reservation;
use App\Models\walkins;
use Illuminate\Support\Facades\Log;
use App\Models\orders;

class CashierController extends Controller
{
    public function home()
    {
        $now = Carbon::now();

        $tables = DB::table('tables')->get();
        $menuItems = DB::table('menu')->get();

        $reservations = Reservation::with('table')
            ->where('status', 'Active')
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>', $now)
            ->whereDoesntHave('transactions')
            ->get();

        $walkins = walkins::with('table')
            ->where('status', 'Active')
            ->whereNull('ended_at')
            ->get();

        $reservationIds = $reservations->pluck('id')->toArray();
        $walkinIds = $walkins->pluck('id')->toArray();

        $orders = orders::with('menu')
            ->where(function ($query) use ($reservationIds, $walkinIds) {
                $query->whereIn('reservation_id', $reservationIds)
                    ->orWhereIn('walk_in_id', $walkinIds);
            })
            ->get()
            ->groupBy(function ($order) {
                return $order->reservation_id ?? ('walkin_' . $order->walk_in_id);
            });

        $occupiedTables = [];
        foreach ($tables as $table) {
            $res = $reservations->firstWhere('table_id', $table->id);
            $session = $walkins->firstWhere('table_id', $table->id);

            if ($res) {
                $table->current_reservation_id = $res->id;
                $table->current_session_id = null;
                $table->is_walk_in = false;

                $endTime = Carbon::parse($res->ended_at);
                $secondsRemaining = $now->diffInSeconds($endTime);
                $table->remaining_seconds = min(7200, max(0, $secondsRemaining));

                $table->current_orders = $orders[$res->id] ?? [];
                $occupiedTables[] = $table->table_number;
            } elseif ($session) {
                $table->current_reservation_id = null;
                $table->current_session_id = $session->id;
                $table->is_walk_in = true;

                $startTime = Carbon::parse($session->started_at);
                $elapsed = $now->diffInSeconds($startTime);
                $table->elapsed_seconds = min(7200, $elapsed);
                $table->remaining_seconds = null;

                $table->current_orders = $orders['walkin_' . $session->id] ?? [];
                $occupiedTables[] = $table->table_number;
            } else {
                $table->current_reservation_id = null;
                $table->current_session_id = null;
                $table->is_walk_in = false;
                $table->remaining_seconds = null;
                $table->elapsed_seconds = null;
                $table->current_orders = [];
            }
        }

        $menuDiscounts = DB::table('menu_discounts')->get()->groupBy('menu_id');
        $menuPricesMap = [];
        $menuData = [];

        foreach ($menuItems as $item) {
            $discounts = $menuDiscounts[$item->id] ?? collect();
            $studentDisc = $discounts->firstWhere('discount_type', 'Student');
            $govtDisc    = $discounts->firstWhere('discount_type', 'Government Employee');
            $seniorDisc  = $discounts->firstWhere('discount_type', 'Senior Citizen');
            $pwdDisc     = $discounts->firstWhere('discount_type', 'PWD');

            $row = [
                'menu_item'     => $item->menu_item,
                'regular'       => $item->regular_price,
                'student'       => $studentDisc ? round($item->regular_price * (1 - ($studentDisc->discount_percentage / 100)), 2) : null,
                'govt_employee' => $govtDisc    ? round($item->regular_price * (1 - ($govtDisc->discount_percentage / 100)), 2)    : null,
                'senior'        => $seniorDisc  ? round($item->regular_price * (1 - ($seniorDisc->discount_percentage / 100)), 2)  : null,
                'pwd'           => $pwdDisc     ? round($item->regular_price * (1 - ($pwdDisc->discount_percentage / 100)), 2)     : null,
                'has_discount'  => $item->has_customer_discount,
            ];

            $menuPricesMap[$item->menu_item] = $row;
            $menuData[] = $row;
        }

        $groupedMenu = [];
        foreach ($menuItems as $item) {
            $groupedMenu[$item->category][] = $item;
        }

        return view('cashier.home', compact(
            'tables',
            'menuItems',
            'reservations',
            'walkins',
            'menuPricesMap',
            'groupedMenu',
            'occupiedTables',
            'menuData'
        ));
    }


    public function getOrders($id)
    {
        $reservation = DB::table('reservations')
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('reservation_payment_details', 'reservations.id', '=', 'reservation_payment_details.reservation_id')
            ->where('reservations.id', $id)
            ->select(
                'reservations.id as reservation_id',
                'reservations.started_at as reservation_time',
                'reservation_payment_details.advance_payment',
                'customers.name as customer_name',
                'customers.contact_number',
                'customers.id_type',
                'reservations.pax',
                'reservations.customer_id'
            )
            ->first();

        if (!$reservation) {
            $session = DB::table('walk_ins')
                ->join('customers', 'walk_ins.customer_id', '=', 'customers.id')
                ->where('walk_ins.id', $id)
                ->select(
                    'walk_ins.id as reservation_id',
                    'walk_ins.started_at as reservation_time',
                    DB::raw('0 as advance_payment'),
                    'customers.name as customer_name',
                    'customers.contact_number',
                    'customers.id_type',
                    'walk_ins.pax',
                    'walk_ins.customer_id'
                )
                ->first();

            if (!$session) {
                return response()->json(['message' => 'Order not found'], 404);
            }
            $reservation = $session;
        }

        $orders = DB::table('orders')
            ->join('menu', 'orders.menu_id', '=', 'menu.id')
            ->where(function ($query) use ($id) {
                $query->where('orders.reservation_id', $id)
                    ->orWhere('orders.walk_in_id', $id);
            })
            ->select(
                'orders.id as order_id',
                'orders.price as order_price',
                'orders.quantity',
                'menu.menu_item as order_name',
                'menu.regular_price',
                'menu.has_customer_discount'
            )
            ->get()
            ->map(function ($order) {
                $order->unit_price = $order->order_price / $order->quantity;
                return $order;
            });

        return response()->json([
            'reservation_id'   => $reservation->reservation_id,
            'customer_name'    => $reservation->customer_name,
            'contact_number'   => $reservation->contact_number,
            'id_type'          => $reservation->id_type,
            'customer_id'      => $reservation->customer_id,
            'pax'              => $reservation->pax,
            'reservation_time' => $reservation->reservation_time,
            'advance_payment'  => floatval($reservation->advance_payment ?? 0),
            'orders'           => $orders
        ]);
    }


    private function calculateDiscountedPrice($menuItem, $regularPrice, $customerType)
    {
        if ($customerType === 'regular' || $customerType === 'none') {
            return $regularPrice;
        }

        $discountTypeMap = [
            'student' => 'Student',
            'govt_employee' => 'Government Employee',
            'pwd_senior' => ['Senior Citizen', 'PWD']
        ];

        $discountType = $discountTypeMap[$customerType] ?? null;

        if (!$discountType) {
            return $regularPrice;
        }

        $menuId = DB::table('menu')->where('menu_item', $menuItem)->value('id');

        if (!$menuId) {
            return $regularPrice;
        }

        if (is_array($discountType)) {
            $discount = DB::table('menu_discounts')
                ->where('menu_id', $menuId)
                ->whereIn('discount_type', $discountType)
                ->first();
        } else {
            $discount = DB::table('menu_discounts')
                ->where('menu_id', $menuId)
                ->where('discount_type', $discountType)
                ->first();
        }

        if ($discount) {
            return round($regularPrice * (1 - ($discount->discount_percentage / 100)), 2);
        }

        return $regularPrice;
    }

    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'reservation_id'               => 'required|integer|exists:reservations,id',
                'subtotal'                     => 'nullable|numeric|min:0',
                'advance_payment'              => 'nullable|numeric|min:0',
                'total'                        => 'required|numeric|min:0',
                'orders'                       => 'required|array|min:1',
                'orders.*.order_id'            => 'required|integer',
                'orders.*.order_name'          => 'required|string',
                'orders.*.quantity'            => 'required|integer|min:1',
                'orders.*.price'               => 'required|numeric|min:0',
                'customer_data'                => 'nullable|array',
                'customer_data.*.name'         => 'required_with:customer_data|string|max:255',
                'customer_data.*.id_type'      => 'nullable|string|max:255',
                'customer_data.*.customer_type' => 'nullable|string|in:student,govt_employee,pwd_senior,regular',
                'customer_data.*.item_index'   => 'required_with:customer_data|integer',
                'cash_received'                => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $reservation = Reservation::with('customer')->find($request->reservation_id);
            if (!$reservation) {
                throw new Exception('Reservation not found');
            }

            $uniqueCustomers = [];
            $customerMap = [];

            if (!empty($request->customer_data)) {
                foreach ($request->customer_data as $customerInfo) {
                    $customerKey = trim($customerInfo['name']) . '|' . ($customerInfo['id_type'] ?? '');

                    if (!isset($uniqueCustomers[$customerKey])) {
                        $customer = Customers::create([
                            'name'    => trim($customerInfo['name']),
                            'id_type' => $customerInfo['id_type'] ?? null,
                        ]);
                        $uniqueCustomers[$customerKey] = $customer;
                    }

                    $customerMap[$customerInfo['item_index']] = [
                        'customer'      => $uniqueCustomers[$customerKey],
                        'customer_type' => $customerInfo['customer_type'] ?? 'regular'
                    ];
                }
            }

            $mainCustomer = $reservation->customer;
            if (!$mainCustomer) {
                $mainCustomer = Customers::create([
                    'name' => 'Walk-in Customer',
                ]);
                $reservation->update(['customer_id' => $mainCustomer->id]);
            }

            $ordersTotal         = 0;
            $totalDiscountAmount = 0;
            $processedOrders     = [];
            $itemIndex           = 0;

            foreach ($request->orders as $orderData) {
                $orderDetail = DB::table('orders')
                    ->join('menu', 'orders.menu_id', '=', 'menu.id')
                    ->where('orders.id', $orderData['order_id'])
                    ->select('orders.*', 'menu.regular_price', 'menu.menu_item', 'menu.has_customer_discount')
                    ->first();

                if (!$orderDetail) {
                    throw new Exception("Order not found: {$orderData['order_id']}");
                }

                for ($i = 0; $i < $orderData['quantity']; $i++) {
                    $linkedCustomer = $mainCustomer;
                    $customerType   = 'regular';

                    if (isset($customerMap[$itemIndex])) {
                        $linkedCustomer = $customerMap[$itemIndex]['customer'];
                        $customerType   = $customerMap[$itemIndex]['customer_type'];
                    }

                    $regularPrice    = $orderDetail->regular_price;
                    $discountedPrice = $this->calculateDiscountedPrice(
                        $orderDetail->menu_item,
                        $regularPrice,
                        $customerType
                    );
                    $discountAmount = $regularPrice - $discountedPrice;

                    $ordersTotal         += $regularPrice;
                    $totalDiscountAmount += $discountAmount;

                    $processedOrders[] = [
                        'order_id'        => $orderData['order_id'],
                        'item_name'       => $orderData['order_name'],
                        'quantity'        => 1,
                        'discount_amount' => $discountAmount,
                        'customer_id'     => $linkedCustomer->id,
                    ];

                    $itemIndex++;
                }
            }

            $advancePayment = floatval($request->advance_payment ?? $reservation->advance_payment ?? 0);
            $grandTotal     = $ordersTotal - $totalDiscountAmount;
            $toPay          = max(0, $grandTotal - $advancePayment);
            $cashReceived   = floatval($request->cash_received ?? 0);
            $change         = max(0, $cashReceived - $toPay);

            $transactionData = [
                'transaction_no'  => 'TEMP',
                'orders_total'    => $ordersTotal,
                'discount_total'  => $totalDiscountAmount,
                'grand_total'     => $grandTotal,
                'advance_payment' => $advancePayment,
                'to_pay'          => $toPay,
                'cash_received'   => $cashReceived,
                'change'          => $change,
                'payment_method'  => 'Cash',
                'status'          => 'Completed',
                'reservation_id'  => $reservation->id,
                'customer_id'     => $mainCustomer->id,
                'cashier_id'      => Auth::id(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            $transactionId = DB::table('transactions')->insertGetId($transactionData);
            $transactionNo = date('Ymd') . '-' . str_pad($transactionId, 6, '0', STR_PAD_LEFT);

            DB::table('transactions')->where('id', $transactionId)->update([
                'transaction_no' => $transactionNo,
                'updated_at'     => now(),
            ]);

            foreach ($processedOrders as $order) {
                DB::table('transaction_details')->insert([
                    'transaction_id'  => $transactionId,
                    'order_id'        => $order['order_id'],
                    'customer_id'     => $order['customer_id'],
                    'item_name'       => $order['item_name'],
                    'quantity'        => $order['quantity'],
                    'discount_amount' => $order['discount_amount'],
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);
            }

            $reservation->update(['status' => 'Completed']);

            $walkIn = DB::table('walk_ins')->where('id', $reservation->id)->first();


            if ($walkIn) {
                DB::table('walk_ins')
                    ->where('id', $reservation->id)
                    ->update([
                        'status' => 'Completed',
                        'ended_at' => now(),
                        'updated_at' => now()
                    ]);
            }

            DB::table('orders')
                ->where('reservation_id', $reservation->id)
                ->where('status', 'Pending')
                ->update(['status' => 'Served', 'updated_at' => now()]);

            DB::commit();

            return response()->json([
                'success'              => true,
                'message'              => 'Payment processed successfully',
                'transaction_no'       => $transactionNo,
                'transaction_id'       => $transactionId,
                'orders_total'         => $ordersTotal,
                'discount_total'       => $totalDiscountAmount,
                'grand_total'          => $grandTotal,
                'advance_payment'      => $advancePayment,
                'to_pay'               => $toPay,
                'cash_received'        => $cashReceived,
                'change'              => $change,
                'processed_items'      => count($processedOrders),
                'discounted_customers' => count($uniqueCustomers)
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
            return response()->json(['exists' => false, 'error' => 'Unable to check customer']);
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
}
