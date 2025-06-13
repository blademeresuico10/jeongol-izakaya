<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReceptionistController extends Controller
{
    public function home()
    {
        $users = DB::table('users')->get();
        $tables = DB::table('tables')->get(); 
        $menuItems = DB::table('menu')->get(); 
        return view('receptionist.home', compact('menuItems', 'tables'));
    }

    public function storeReservation(Request $request)
    {
        try {
            $data = $request->validate([
                'table_id'      => 'required|exists:tables,id',
                'customer_name' => 'required|string',
                'pax'           => 'required|integer|min:1',
                'arrival_time'  => 'required|date_format:H:i',
                'notes'         => 'nullable|string',
                'orders'        => 'nullable|array',
                'orders.*.item' => 'string',
                'orders.*.qty'  => 'integer|min:1',
            ]);

            $userId = Auth::id(); 

            // 1. Handle customer record (create or fetch)
            $customer = DB::table('customers')->where('name', $data['customer_name'])->first();
            if (!$customer) {
                $customerId = DB::table('customers')->insertGetId([
                    'name'       => $data['customer_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $customerId = $customer->id;
            }

            // 2. Fetch table number from table_id
            $table = DB::table('tables')->where('id', $data['table_id'])->first();
            if (!$table) {
                return response()->json(['success' => false, 'message' => 'Table not found.']);
            }

            // 3. Create reservation
            $reservation = Reservation::create([
                'pax'              => $data['pax'],
                'advance_payment'  => 0.00,
                'reservation_time' => now()->setTimeFromTimeString($data['arrival_time']),
                'table_number'     => $table->table_number,
                'notes'            => $data['notes'] ?? null,
                'customer_id'      => $customerId,
                'user_id'          => $userId,
            ]);

            // 4. Save order details
            if (!empty($data['orders'])) {
                foreach ($data['orders'] as $order) {
                    $menu = DB::table('menu')->where('menu_item', $order['item'])->first();
                    if ($menu) {
                        DB::table('order_details')->insert([
                            'reservation_id' => $reservation->id,
                            'menu_id'        => $menu->id,
                            'quantity'       => $order['qty'],
                            'customer_id'    => $customerId,
                            'user_id'        => $userId,
                            'date'           => now(),
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
                'error' => $e->getMessage()
            ]);
        }
    }

    public function reservations()
    {
        $reservations = DB::table('reservations')
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->select(
                'reservations.table_number',
                'reservations.pax',
                'reservations.reservation_time',
                'customers.name as customer_name'
            )
            ->get();

        return view('receptionist.view_reservation', compact('reservations'));
    }





}

   
    
    

