<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Reservation;

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

        return view('customer.place_reservation', compact('tables', 'menuItems'));
    }

    public function storeReservation(Request $request)
    {
        $data = $request->json()->all();

        try {
            $validated = validator($data, [
                'table_id'        => 'required|exists:tables,id',
                'customer_name'   => 'required|string',
                'contact_number' => 'nullable|string|max:12',
                'pax'             => 'required|integer|min:1',
                'reserved_date'   => 'required|date',
                'arrival_time'    => 'required|date_format:H:i',
                'orders'          => 'nullable|array',
                'orders.*.item'   => 'string',
                'orders.*.qty'    => 'integer|min:1',
                'advance_payment' => 'nullable|numeric|min:0',
                'orders.*.notes'  => 'nullable|string',
            ])->validate();

            $userId = Auth::id();

            $customer = DB::table('customers')
                ->where('name', $validated['customer_name'])
                ->where('contact_number', $validated['contact_number'] ?? '')
                ->first();

            if (!$customer) {
                $customerId = DB::table('customers')->insertGetId([
                    'name'       => $validated['customer_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $customerId = $customer->id;
            }

            $table = DB::table('tables')->where('id', $validated['table_id'])->first();

            $reservedDateTime = Carbon::parse($validated['reserved_date'].' '.$validated['arrival_time']);
            $endDateTime = $reservedDateTime->copy()->addHours(2);

            if ($reservedDateTime->toDateString() < now()->toDateString()) {
                return response()->json(['success' => false, 'message' => 'Cannot reserve on a past day.']);
            }

            $conflict = DB::table('reservations')
                ->where('table_number', $table->table_number)
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
                    $search = $order['item'] . ($isLunch ? ' Lunch' : ' Dinner');
                    $menu = DB::table('menu')->where('menu_item', $search)->first();
                    if ($menu) {
                        $totalPrice += $menu->price * $order['qty'];
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
            ]);

            if (!empty($validated['orders'])) {
                foreach ($validated['orders'] as $order) {
                    if (empty($order['item']) || empty($order['qty']) || $order['qty'] < 1) {
                        continue;
                    }

                    $search = $order['item'] . ($isLunch ? ' Lunch' : ' Dinner');
                    $menu = DB::table('menu')->where('menu_item', $search)->first();

                    if ($menu) {
                        DB::table('order_details')->insert([
                            'order_price'    => $menu->price * $order['qty'],
                            'reservation_id' => $reservation->id,
                            'menu_id'        => $menu->id,
                            'quantity'       => $order['qty'],
                            'notes'          => !empty($order['notes']) ? $order['notes'] : null,
                            'customer_id'    => $customerId,
                            'user_id'        => $userId,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ]);
                    }
                }
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
}

