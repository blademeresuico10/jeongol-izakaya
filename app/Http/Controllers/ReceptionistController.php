<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReceptionistController extends Controller
{
    public function home()
    {
        $tables = DB::table('tables')->get(); 
        $menuItems = DB::table('menu')->get();

        // Get current time and today's reservations
        $now = Carbon::now();
        $reservations = DB::table('reservations')
            ->whereDate('reservation_time', $now->toDateString())
            ->get();

        return view('receptionist.home', compact('tables', 'menuItems', 'reservations'));
    }

    public function storeReservation(Request $request)
{
    // Accept JSON body
    $data = $request->json()->all();

    try {
        $validated = validator($data, [
            'table_id'        => 'required|exists:tables,id',
            'customer_name'   => 'required|string',
            'pax'             => 'required|integer|min:1',
            'reserved_date'   => 'required|date',
            'arrival_time'    => 'required|date_format:H:i',
            'notes'           => 'nullable|string',
            'orders'          => 'nullable|array',
            'orders.*.item'   => 'string',
            'orders.*.qty'    => 'integer|min:1',
            'advance_payment' => 'nullable|numeric|min:0',
        ])->validate();

        // continue same as before
        $userId = Auth::id();

        // Find or create customer
        $customer = DB::table('customers')->where('name', $validated['customer_name'])->first();
        if (!$customer) {
            $customerId = DB::table('customers')->insertGetId([
                'name'       => $validated['customer_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $customerId = $customer->id;
        }

        // Get table number
        $table = DB::table('tables')->where('id', $validated['table_id'])->first();

        $reservedDateTime = Carbon::parse($validated['reserved_date'].' '.$validated['arrival_time']);
        $endDateTime = $reservedDateTime->copy()->addHours(2);

        if ($reservedDateTime->toDateString() < now()->toDateString()) {
    return response()->json(['success' => false, 'message' => 'Cannot reserve on a past day.']);
}

        // Check conflict
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

        // Calculate total
        $totalPrice = 0;
        if (!empty($validated['orders'])) {
            foreach ($validated['orders'] as $order) {
                $menu = DB::table('menu')->where('menu_item', $order['item'])->first();
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
                $menu = DB::table('menu')->where('menu_item', $order['item'])->first();
                if ($menu) {
                    DB::table('order_details')->insert([
                        'order_price'    => $menu->price * $order['qty'],
                        'reservation_id' => $reservation->id,
                        'menu_id'        => $menu->id,
                        'quantity'       => $order['qty'],
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


    public function reservations(Request $request)
    {
        $date = $request->query('date', 'today');

        if ($date === 'tomorrow') {
            $targetDate = Carbon::tomorrow()->toDateString();
        } elseif ($date === 'next') {
            $targetDate = Carbon::today()->addDays(2)->toDateString();
        } else {
            $targetDate = Carbon::today()->toDateString();
        }

        $reservations = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('order_details', 'reservations.id', '=', 'order_details.reservation_id')
            ->leftJoin('menu', 'order_details.menu_id', '=', 'menu.id')
            ->select(
                'reservations.id as reservation_id',
                'reservations.table_number',
                'reservations.pax',
                'reservations.reservation_time',
                'customers.name as customer_name',
                'menu.menu_item',
                'order_details.quantity'
            )
            ->whereDate('reservations.reservation_time', $targetDate)
            ->orderBy('reservations.reservation_time')
            ->get();

        return view('receptionist.view_reservation', compact('reservations'));
    }




}

   
    
    

