<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\reservation;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.index');
    }

    public function place_reservation(Request $request)
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

        return view('customer.place_reservation', compact('tables', 'menuItems', 'reservations', 'menuPricesMap', 'groupedMenu'));
    }

    public function storeReservation(Request $request)
    {


        $validator = validator($request->all(), [
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
            'proof'              => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:9120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $userId = Auth::id() ?? null;

            $customer = DB::table('customers')
                ->where('name', $validated['customer_name'])
                ->where('contact_number', $validated['contact_number'] ?? '')
                ->first();

            $customerId = $customer
                ? $customer->id
                : DB::table('customers')->insertGetId([
                    'name'           => $validated['customer_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

            $table = DB::table('tables')->where('id', $validated['table_id'])->first();

            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Invalid table.'], 404);
            }

            $reservedDateTime = Carbon::parse($validated['reserved_date'] . ' ' . $validated['arrival_time']);
            $endDateTime = $reservedDateTime->copy()->addHours(2);

            if ($reservedDateTime->lt(now())) {
                return response()->json(['success' => false, 'message' => 'Cannot reserve in the past.']);
            }

            $conflict = DB::table('reservations')
                ->where('table_number', $table->table_number)
                ->where('status', 'Accepted')
                ->where(function ($query) use ($reservedDateTime, $endDateTime) {
                    $query->where('reservation_time', '<', $endDateTime)
                        ->where('reservation_end_time', '>', $reservedDateTime);
                })
                ->exists();

            if ($conflict) {
                return response()->json(['success' => false, 'message' => 'Time slot already taken.']);
            }

            $totalPrice = 0;
            if (!empty($validated['orders'])) {
                foreach ($validated['orders'] as $order) {
                    $menu = DB::table('menu')->find($order['menu_id']);
                    if ($menu) {
                        $totalPrice += $menu->price * $order['quantity'];
                    }
                }
            }

            $reservation = Reservation::create([
                'pax'                  => $validated['pax'],
                'advance_payment'      => $validated['advance_payment'] ?? 0.00,
                'reservation_time'     => $reservedDateTime,
                'reservation_end_time' => $endDateTime,
                'table_number'         => $table->table_number,
                'notes'                => $validated['notes'] ?? null,
                'customer_id'          => $customerId,
                'user_id'              => $userId,
                'total_price'          => $totalPrice,
                'status'               => 'Pending',
            ]);

            if ($request->has('payment_method')) {
                $proofPath = null;

                if ($request->hasFile('proof')) {
                    $proofPath = $request->file('proof')->store('payment_proofs', 'public');
                }

                DB::table('reservation_payments')->insert([
                    'reservation_id'  => $reservation->id,
                    'registered_name' => $request->input('registered_name'),
                    'number'          => $request->input('number'),
                    'amount'          => $request->input('amount') ?? 0.00,
                    'method'          => $request->input('payment_method'),
                    'ref_no'          => $request->input('ref_no'),
                    'proof_path'      => $proofPath,
                    'status'          => 'pending',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }



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

            foreach (\App\Models\User::where('role', 'receptionist')->get() as $user) {
                $user->notify(new \App\Notifications\ReservationPaid($reservation));
            }

            return response()->json([
                'success' => true,
                'message' => 'Reservation placed successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Reservation Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Reservation failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
