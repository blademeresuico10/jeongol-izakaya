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
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class CashierController extends Controller
{
    public function home()
    {
        $now = Carbon::now();

        $tables = DB::table('tables')->get();
        $menuItems = DB::table('menu')->get();

        $reservations = \App\Models\reservation::with('table')
            ->whereDate('reservation_time', $now->toDateString())
            ->where('reservation_time', '<=', $now)
            ->where('reservation_end_time', '>=', $now)
            ->where('status', 'Accepted')
            ->whereDoesntHave('transactions')
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
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('reservation_payment_details', 'reservations.id', '=', 'reservation_payment_details.reservation_id')
            ->where('reservations.id', $reservationId)
            ->select(
                'reservations.id as reservation_id',
                'reservations.reservation_time',
                'reservation_payment_details.advance_payment',
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
            'advance_payment'  => floatval($reservation->advance_payment ?? 0),
            'orders'           => $orders
        ]);
    }


    private function calculateMenuPrice($menuItem, $customerType)
    {
        switch ($customerType) {
            case 'student':
                return $menuItem->student_price ?? $menuItem->regular_price;
            case 'govt_employee':
                return $menuItem->govt_employee_price ?? $menuItem->regular_price;
            case 'pwd_senior':
                return $menuItem->pwd_senior_price ?? $menuItem->regular_price;
            default:
                return $menuItem->regular_price;
        }
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
                'orders.*.order_detail_id'     => 'required|integer',
                'orders.*.order_name'          => 'required|string',
                'orders.*.quantity'            => 'required|integer|min:1',
                'orders.*.price'               => 'required|numeric|min:0',
                'customer_data'                => 'nullable|array',
                'customer_data.*.name'         => 'required_with:customer_data|string|max:255',
                'customer_data.*.id_type'      => 'nullable|string|max:255',
                'customer_data.*.customer_type' => 'nullable|string|in:student,govt_employee,pwd_senior,regular',
                'customer_data.*.item_index'   => 'required_with:customer_data|integer',
                'discounted_persons'           => 'nullable|array',
                'cash_received'                => 'nullable|numeric|min:0',
                'change'                       => 'nullable|numeric|min:0',
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

            $customerMap = [];
            if (!empty($request->customer_data)) {
                foreach ($request->customer_data as $customerInfo) {
                    $customer = Customers::create([
                        'name'    => trim($customerInfo['name']),
                        'id_type' => $customerInfo['id_type'] ?? null,
                    ]);

                    $customerMap[$customerInfo['item_index']] = [
                        'customer'      => $customer,
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

            $subtotal            = 0;
            $totalDiscountAmount = 0;
            $processedOrders     = [];
            $itemIndex           = 0;

            foreach ($request->orders as $orderData) {
                $orderDetail = DB::table('order_details')
                    ->join('menu', 'order_details.menu_id', '=', 'menu.id')
                    ->where('order_details.id', $orderData['order_detail_id'])
                    ->select('order_details.*', 'menu.*')
                    ->first();

                if (!$orderDetail) {
                    throw new Exception("Order detail not found: {$orderData['order_detail_id']}");
                }

                for ($i = 0; $i < $orderData['quantity']; $i++) {
                    $linkedCustomer = $mainCustomer;
                    $customerType   = 'regular';

                    if (isset($customerMap[$itemIndex])) {
                        $linkedCustomer = $customerMap[$itemIndex]['customer'];
                        $customerType   = $customerMap[$itemIndex]['customer_type'];
                    }

                    $regularPrice    = $orderDetail->regular_price;
                    $discountedPrice = $this->calculateMenuPrice($orderDetail, $customerType);
                    $discount        = $regularPrice - $discountedPrice;

                    $subtotal            += $regularPrice;
                    $totalDiscountAmount += $discount;

                    $processedOrders[] = [
                        'order_detail_id' => $orderData['order_detail_id'],
                        'item_name'       => $orderData['order_name'],
                        'quantity'        => 1,
                        'discount_amount' => $discount,
                        'customer_id'     => $linkedCustomer->id,
                    ];

                    $itemIndex++;
                }
            }

            $advancePayment      = floatval($request->advance_payment ?? $reservation->advance_payment ?? 0);
            $totalAfterDiscounts = $subtotal - $totalDiscountAmount;
            $toPay               = max(0, $totalAfterDiscounts - $advancePayment);
            $cashReceived        = floatval($request->cash_received ?? 0);
            $change              = floatval($request->change ?? 0);

            $transactionData = [
                'transaction_no'  => 'TEMP',
                'orders_total'    => $subtotal,
                'discount_total'  => $totalDiscountAmount,
                'grand_total'     => $totalAfterDiscounts,
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

            // Update transaction number
            DB::table('transactions')->where('id', $transactionId)->update([
                'transaction_no' => $transactionNo,
                'updated_at'     => now(),
            ]);

            // Insert transaction details
            foreach ($processedOrders as $order) {
                DB::table('transaction_details')->insert([
                    'transaction_id'  => $transactionId,
                    'order_detail_id' => $order['order_detail_id'],
                    'customer_id'     => $order['customer_id'],
                    'item_name'       => $order['item_name'],
                    'quantity'        => $order['quantity'],
                    'discount_amount' => $order['discount_amount'],
                    'created_at'      => now(),
                    'updated_at'      => now()
                ]);
            }

            $reservation->update(['status' => 'Completed']);
            DB::table('order_details')
                ->where('reservation_id', $reservation->id)
                ->where('status', 'Pending')
                ->update([
                    'status'     => 'Served',
                    'updated_at' => now()
                ]);

            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Payment processed successfully',
                'transaction_no'  => $transactionNo,
                'transaction_id'  => $transactionId,
                'orders_total'    => $subtotal,             
                'discount_total'  => $totalDiscountAmount,
                'grand_total'     => $totalAfterDiscounts,  
                'advance_payment' => $advancePayment,
                'to_pay'          => $toPay,
                'cash_received'   => $cashReceived,
                'change'          => $change,
                'processed_items' => count($processedOrders),
                'discounted_customers' => count($customerMap)
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



    public function printReceipt(Request $request)
    {
        try {


            $request->validate([
                'customer_name' => 'required|string',
                'total' => 'required|numeric|min:0',
                'cash_received' => 'required|numeric|min:0',
                'change' => 'required|numeric|min:0',
                'orders' => 'required|array|min:1',
                'orders.*.order_name' => 'required|string',
                'orders.*.quantity' => 'required|integer|min:1',
                'orders.*.price' => 'required|numeric|min:0',
            ]);

            $printer = $this->initializePrinter();

            if (!$printer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt printer not available'
                ]);
            }

            $this->printReceiptContent($printer, $request->all());

            $printer->close();


            return response()->json([
                'success' => true,
                'message' => 'Receipt printed successfully'
            ]);
        } catch (Exception $e) {


            return response()->json([
                'success' => false,
                'message' => 'Printing failed: ' . $e->getMessage()
            ]);
        }
    }

    public function updateAdvance(Request $request, $id)
    {
        $request->validate([
            'advance_payment' => 'required|numeric|min:0',
        ]);

        $reservation = \App\Models\Reservation::with('payment')->findOrFail($id);

        $reservation->advance_payment = $request->advance_payment;
        $reservation->save();

        if ($reservation->payment) {
            $reservation->payment->amount = $request->advance_payment;
            $reservation->payment->save();
        }

        return response()->json([
            'success' => true,
            'newAmountDue' => $reservation->total - $reservation->advance_payment,
        ]);
    }

    private function initializePrinter()
    {
        try {
            $connector = new WindowsPrintConnector("POS-80");
            return new Printer($connector);
        } catch (Exception $e) {
            try {
                $connector = new NetworkPrintConnector("192.168.1.100", 9100);
                return new Printer($connector);
            } catch (Exception $e2) {
                try {
                    $connector = new FilePrintConnector("LPT1");
                    return new Printer($connector);
                } catch (Exception $e3) {
                    return null;
                }
            }
        }
    }

    private function printReceiptContent($printer, $data)
    {
        try {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("Jeongol Izakaya Hotpot % Grill\n");
            $printer->selectPrintMode();

            $printer->text(date('Y-m-d H:i:s A') . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Customer: " . substr($data['customer_name'], 0, 25) . "\n");
            $printer->text(str_repeat("-", 32) . "\n");

            $printer->text("ITEMS:\n");
            $printer->text(str_repeat("-", 32) . "\n");

            $totalItems = 0;
            foreach ($data['orders'] as $order) {
                $itemName = substr($order['order_name'], 0, 20);
                $qty = $order['quantity'];
                $price = $order['price'];
                $total = $qty * $price;
                $totalItems += $qty;

                $printer->text($itemName . "\n");

                $qtyPrice = sprintf("%d x P%.2f", $qty, $price);
                $totalStr = sprintf("P%.2f", $total);
                $spaces = 32 - strlen($qtyPrice) - strlen($totalStr);
                $spaces = max(1, $spaces);
                $printer->text($qtyPrice . str_repeat(" ", $spaces) . $totalStr . "\n");
            }

            $printer->text(str_repeat("-", 32) . "\n");

            $total = $data['total'];
            $cash = $data['cash_received'];
            $change = $data['change'];

            $this->printLine($printer, "TOTAL ITEMS:", (string)$totalItems);
            $printer->text(str_repeat("=", 32) . "\n");
            $this->printLine($printer, "TOTAL:", sprintf("P%.2f", $total));
            $this->printLine($printer, "CASH:", sprintf("P%.2f", $cash));
            $this->printLine($printer, "CHANGE:", sprintf("P%.2f", $change));

            $printer->text(str_repeat("=", 32) . "\n");

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("\nThank you for dining with us!\n");
            $printer->text("Please come again\n");

            $printer->text("\n\n");

            try {
                $printer->cut();
            } catch (Exception $e) {
                $printer->text("\n\n\n");
            }

            try {
                $printer->pulse();
            } catch (Exception $e) {
            }
        } catch (Exception $e) {

            throw $e;
        }
    }

    private function printLine($printer, $left, $right)
    {
        $left = substr($left, 0, 20);
        $right = substr($right, 0, 10);
        $spaces = 32 - strlen($left) - strlen($right);
        $spaces = max(1, $spaces);
        $printer->text($left . str_repeat(" ", $spaces) . $right . "\n");
    }


    public function testPrinter()
    {
        try {
            $printer = $this->initializePrinter();

            if (!$printer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot connect to printer'
                ]);
            }

            $printer->text("Printer test successful!\n");
            $printer->text(date('Y-m-d H:i:s') . "\n");
            $printer->text("Connection working properly.\n\n");
            $printer->cut();
            $printer->close();

            return response()->json([
                'success' => true,
                'message' => 'Test receipt printed successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ]);
        }
    }
}
