<?php

namespace App\Http\Controllers;

use App\Models\customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\reservation;
use App\Models\walkin;
use Illuminate\Support\Facades\Log;
use App\Models\orders;

class CashierController extends Controller
{
    public function home()
    {
        $now = Carbon::now();

        $tables = DB::table('tables')->get();
        $menuItems = DB::table('menu')->get();

        $reservations = reservation::with('table')
            ->where('status', 'Active')
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>', $now)
            ->whereDoesntHave('transactions')
            ->get();

        $walkin = walkin::with('table')
            ->where('status', 'Active')
            ->where('started_at', '<=', $now)
            ->where('ended_at', '>', $now)
            ->whereDoesntHave('transactions')
            ->get();

        $reservationIds = $reservations->pluck('id')->toArray();
        $walkinIds = $walkin->pluck('id')->toArray();

        $orders = orders::with('menu')
            ->where(function ($query) use ($reservationIds, $walkinIds) {
                if (!empty($reservationIds)) {
                    $query->whereIn('reservation_id', $reservationIds);
                }
                if (!empty($walkinIds)) {
                    $query->orWhereIn('walk_in_id', $walkinIds);
                }
            })
            ->get()
            ->groupBy(function ($order) {
                return $order->reservation_id ?? ('walkin_' . $order->walk_in_id);
            });

        $occupiedTables = [];

        foreach ($tables as $table) {
            $res = $reservations->firstWhere('table_id', $table->id);
            $session = $walkin->firstWhere('table_id', $table->id);

            if ($res) {
                $table->current_reservation_id = $res->id;
                $table->current_session_id = null;
                $table->is_walk_in = false;

                $endTime = Carbon::parse($res->ended_at);

                $secondsRemaining = $now->diffInSeconds($endTime, false);
                $table->remaining_seconds = max(0, $secondsRemaining);

                $orderKey = $res->id;
                $table->current_orders = $orders[$orderKey] ?? [];

                $occupiedTables[] = $res->table->table_number ?? $table->table_number;
            } elseif ($session) {
                $table->current_reservation_id = null;
                $table->current_session_id = $session->id;
                $table->is_walk_in = true;

                $endTime = Carbon::parse($session->ended_at);

                $secondsRemaining = $now->diffInSeconds($endTime, false);
                $table->remaining_seconds = max(0, $secondsRemaining);

                $orderKey = 'walkin_' . $session->id;
                $table->current_orders = $orders[$orderKey] ?? [];

                $occupiedTables[] = $session->table->table_number ?? $table->table_number;
            } else {
                $table->current_reservation_id = null;
                $table->current_session_id = null;
                $table->is_walk_in = false;
                $table->remaining_seconds = null;
                $table->current_orders = [];
            }
        }

        $menuDiscounts = DB::table('menu_discounts')->get()->groupBy('menu_id');
        $menuData = [];

        foreach ($menuItems as $item) {
            $discounts = $menuDiscounts[$item->id] ?? collect();

            $studentDisc = $discounts->firstWhere('discount_type', 'Student');
            $govtDisc    = $discounts->firstWhere('discount_type', 'Government Employee');
            $seniorDisc  = $discounts->firstWhere('discount_type', 'Senior Citizen');
            $pwdDisc     = $discounts->firstWhere('discount_type', 'PWD');

            $menuData[] = [
                'menu_item'       => $item->menu_item,
                'regular_price'   => (float) $item->regular_price,
                'student_percent' => $studentDisc->discount_percentage ?? null,
                'govt_percent'    => $govtDisc->discount_percentage ?? null,
                'senior_percent'  => $seniorDisc->discount_percentage ?? null,
                'pwd_percent'     => $pwdDisc->discount_percentage ?? null,
                'has_discount'    => (bool) $item->has_customer_discount,
            ];
        }

        $groupedMenu = [];
        foreach ($menuItems as $item) {
            $groupedMenu[$item->category][] = $item;
        }

        return view('cashier.home', compact(
            'tables',
            'menuItems',
            'reservations',
            'walkin',
            'groupedMenu',
            'occupiedTables',
            'menuData'
        ));
    }

    public function getOrders($id)
    {
        $now = Carbon::now();
        $today = Carbon::today();

        $reservation = DB::table('reservations')
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('reservation_payment_details', 'reservations.id', '=', 'reservation_payment_details.reservation_id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->where('reservations.id', $id)
            ->where('reservations.status', 'Active')
            ->whereDate('reservations.started_at', $today)
            ->whereNotExists(function ($query) use ($id) {
                $query->select(DB::raw(1))
                    ->from('transactions')
                    ->whereColumn('transactions.reservation_id', 'reservations.id');
            })
            ->select(
                'reservations.id as reservation_id',
                'reservations.started_at as reservation_time',
                'reservations.ended_at',
                'reservation_payment_details.advance_payment',
                'customers.name as customer_name',
                'customers.id_number',
                'customers.contact_number',
                'customers.id_type',
                'reservations.pax',
                'reservations.customer_id',
                'tables.table_number',
                DB::raw("'reservation' as order_type")
            )
            ->first();

        if (!$reservation) {
            $reservation = DB::table('walk_ins')
                ->join('customers', 'walk_ins.customer_id', '=', 'customers.id')
                ->join('tables', 'walk_ins.table_id', '=', 'tables.id')
                ->where('walk_ins.id', $id)
                ->where('walk_ins.status', 'Active')
                ->whereDate('walk_ins.started_at', $today)
                ->whereNotExists(function ($query) use ($id) {
                    $query->select(DB::raw(1))
                        ->from('transactions')
                        ->whereColumn('transactions.walk_in_id', 'walk_ins.id');
                })
                ->select(
                    'walk_ins.id as reservation_id',
                    'walk_ins.started_at as reservation_time',
                    'walk_ins.ended_at',
                    DB::raw('0 as advance_payment'),
                    'customers.name as customer_name',
                    'customers.id_number',
                    'customers.contact_number',
                    'customers.id_type',
                    'walk_ins.pax',
                    'walk_ins.customer_id',
                    'tables.table_number',
                    DB::raw("'walkin' as order_type")
                )
                ->first();

            if (!$reservation) {
                return response()->json([
                    'message' => 'No active session found or already processed'
                ], 404);
            }
        }

        $orders = DB::table('orders')
            ->join('menu', 'orders.menu_id', '=', 'menu.id')
            ->where(function ($query) use ($id, $reservation) {
                if ($reservation->order_type === 'reservation') {
                    $query->where('orders.reservation_id', $id);
                } else {
                    $query->where('orders.walk_in_id', $id);
                }
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

        $endTime = Carbon::parse($reservation->ended_at);
        $isExpired = $now->greaterThan($endTime);

        return response()->json([
            'reservation_id'   => $reservation->reservation_id,
            'customer_name'    => $reservation->customer_name,
            'id_number'        => $reservation->id_number,
            'contact_number'   => $reservation->contact_number,
            'id_type'          => $reservation->id_type,
            'customer_id'      => $reservation->customer_id,
            'pax'              => $reservation->pax,
            'reservation_time' => $reservation->reservation_time,
            'advance_payment'  => floatval($reservation->advance_payment ?? 0),
            'table_number'     => $reservation->table_number,
            'order_type'       => $reservation->order_type,
            'ended_at'         => $reservation->ended_at,
            'is_expired'       => $isExpired,
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
            $discountedPrice = $regularPrice * (1 - ($discount->discount_percentage / 100));

            $decimalPart = $discountedPrice - floor($discountedPrice);
            if ($decimalPart >= 0.5) {
                $discountedPrice = ceil($discountedPrice);
            } else {
                $discountedPrice = floor($discountedPrice);
            }

            return $discountedPrice;
        }

        return $regularPrice;
    }


    public function processPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'reservation_id'               => 'required|integer',
                'order_type'                   => 'required|in:reservation,walkin',
                'subtotal'                     => 'nullable|numeric|min:0',
                'advance_payment'              => 'nullable|numeric|min:0',
                'total'                        => 'required|numeric|min:0',
                'orders'                       => 'required|array|min:1',
                'orders.*.order_id'            => 'required|integer',
                'orders.*.order_name'          => 'required|string',
                'orders.*.quantity'            => 'required|integer|min:1',
                'orders.*.price'               => 'required|numeric|min:0',
                'customer_data'                => 'nullable|array',
                'customer_data.*.name'         => 'nullable|string|max:255', // Changed to nullable
                'customer_data.*.id_number'    => 'required_with:customer_data|string|max:255', // Required
                'customer_data.*.id_type'      => 'nullable|string|max:255',
                'customer_data.*.customer_type' => 'nullable|string|in:student,govt_employee,pwd_senior,regular',
                'customer_data.*.item_index'   => 'required_with:customer_data|integer',
                'cash_received'                => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $isWalkIn = strtolower($request->order_type) === 'walkin';
            $recordId = $request->reservation_id;

            if ($isWalkIn) {
                $record = walkin::with('customer')->find($recordId);
                if (!$record) {
                    throw new Exception('Walk-in not found');
                }
            } else {
                $record = reservation::with('customer')->find($recordId);
                if (!$record) {
                    throw new Exception('Reservation not found');
                }
            }

            $uniqueCustomers = [];
            $customerMap = [];

            if (!empty($request->customer_data)) {
                foreach ($request->customer_data as $customerInfo) {
                    $customerKey = trim($customerInfo['id_number']) . '|' . ($customerInfo['id_type'] ?? '');

                    if (!isset($uniqueCustomers[$customerKey])) {
                        $customer = customers::create([
                            'name' => trim($customerInfo['name']),
                            'id_number' => trim($customerInfo['id_number']),
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

            $mainCustomer = $record->customer;
            if (!$mainCustomer) {
                $mainCustomer = customers::create([
                    'name' => $isWalkIn ? 'Walk-in Customer' : 'Reservation Customer',
                    'id_number' => $isWalkIn ? 'WALKIN-' . now()->format('YmdHis') : 'RES-' . now()->format('YmdHis'),
                ]);
                $record->update(['customer_id' => $mainCustomer->id]);
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

            $advancePayment = 0;
            if (!$isWalkIn) {
                $advancePayment = floatval($request->advance_payment ?? 0);
                if ($advancePayment == 0) {
                    $paymentDetail = DB::table('reservation_payment_details')
                        ->where('reservation_id', $record->id)
                        ->first();
                    $advancePayment = floatval($paymentDetail->advance_payment ?? 0);
                }
            }

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
                'reservation_id'  => $isWalkIn ? null : $recordId,
                'walk_in_id'      => $isWalkIn ? $recordId : null,
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

            $record->update([
                'status' => 'Completed',
                'ended_at' => now()
            ]);

            if ($isWalkIn) {
                DB::table('orders')
                    ->where('walk_in_id', $record->id)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Served', 'updated_at' => now()]);
            } else {
                DB::table('orders')
                    ->where('reservation_id', $record->id)
                    ->where('status', 'Pending')
                    ->update(['status' => 'Served', 'updated_at' => now()]);
            }

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
                'discounted_customers' => count($uniqueCustomers),
                'type'                => $isWalkIn ? 'walk-in' : 'reservation'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function checkCustomer(Request $request)
    {
        try {
            $idNumber = $request->input('id_number');
            $idType = $request->input('id_type');

            if (!$idNumber || !$idType) {
                return response()->json([
                    'exists' => false,
                    'can_use_discount' => false,
                    'error' => 'Missing parameters'
                ], 400);
            }

            // Check if customer exists with same ID number and type
            $customer = customers::where('id_number', $idNumber)
                ->where('id_type', $idType)
                ->first();

            if (!$customer) {
                // Check if this ID number with this specific ID type has used discount today
                $hasUsedDiscountToday = DB::table('transactions')
                    ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                    ->join('customers', 'transaction_details.customer_id', '=', 'customers.id')
                    ->where('customers.id_number', $idNumber)
                    ->where('customers.id_type', $idType)
                    ->where('transaction_details.discount_amount', '>', 0)
                    ->whereDate('transactions.created_at', Carbon::today())
                    ->exists();

                if ($hasUsedDiscountToday) {
                    return response()->json([
                        'exists' => false,
                        'can_use_discount' => false,
                        'message' => 'This ID has already received a discount today!'
                    ]);
                }

                return response()->json([
                    'exists' => false,
                    'can_use_discount' => true,
                    'message' => 'New customer'
                ]);
            }

            $hasDiscountToday = DB::table('transactions')
                ->join('transaction_details', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->join('customers', 'transaction_details.customer_id', '=', 'customers.id')
                ->where('customers.id_number', $idNumber)
                ->where('customers.id_type', $idType)
                ->where('transaction_details.discount_amount', '>', 0)
                ->whereDate('transactions.created_at', Carbon::today())
                ->exists();

            return response()->json([
                'exists' => true,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'can_use_discount' => !$hasDiscountToday,
                'message' => $hasDiscountToday
                    ? 'This ID has already received a discount today with the same ID type'
                    : 'Customer can use discount'
            ]);
        } catch (Exception $e) {
            Log::error('Check customer failed: ' . $e->getMessage());
            return response()->json([
                'exists' => false,
                'can_use_discount' => false,
                'error' => 'Unable to check customer'
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
                    'customers.id_number',
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
