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
        try {
            $data = $request->validate([
                'table_id'      => 'required|exists:tables,id',
                'customer_name' => 'required|string',
                'pax'           => 'required|integer|min:1',
                'arrival_time'  => 'required|date_format:H:i',
                'notes'         => 'nullable|string',
                'orders'        => 'nullable|array',
                'orders.*.item' => 'string',
                'advance_payment' => 'nullable|numeric|min:0',
                'orders.*.qty'  => 'integer|min:1',
            ]);

            $userId = Auth::id(); 

            
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

            // ✅ Check for overlapping reservation for the same table
            $reservedDateTime = now()->setTimeFromTimeString($data['arrival_time']);
            $conflict = DB::table('reservations')
                ->where('table_number', $table->table_number)
                ->whereDate('reservation_time', $reservedDateTime->toDateString())
                ->get()
                ->filter(function ($existing) use ($reservedDateTime) {
                    $existingStart = \Carbon\Carbon::parse($existing->reservation_time);
                    $existingEnd = $existingStart->copy()->addHours(2);
                    $newEnd = $reservedDateTime->copy()->addHours(2);
                    return $reservedDateTime < $existingEnd && $newEnd > $existingStart;
                })
                ->isNotEmpty();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'This table is already reserved during the selected time slot.'
                ]);
            }

            // 3. Create reservation
            $reservation = Reservation::create([
                'pax'              => $data['pax'],
                'advance_payment'  => $data['advance_payment'] ?? 0.00,
                'reservation_time' => $reservedDateTime,
                'table_number'     => $table->table_number,
                'notes'            => $data['notes'] ?? null,
                'customer_id'      => $customerId,
                'user_id'          => $userId,
                'total_price'      => 'required|numeric|min:0',
            ]);

            // 4. Save order details
            if (!empty($data['orders'])) {
                foreach ($data['orders'] as $order) {
                    $menu = DB::table('menu')->where('menu_item', $order['item'])->first();
                    if ($menu) {
                        DB::table('order_details')->insert([
                            'order_price' => $menu->price * $order['qty'],
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

            return response()->json(['success' => true,]);

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
            ->orderBy('reservations.reservation_time')
            ->get();

        return view('receptionist.view_reservation', compact('reservations'));
    }

    public function getAvailableTimeSlots(Request $request)
{
    $tableNumber = $request->query('table_number');
    $date = $request->query('date');

    if (!$tableNumber || !$date) {
        return response()->json([]);
    }

    $allSlots = collect([
        '10:00', '11:00', '12:00', '13:00', '14:00',
        '15:00', '16:00', '17:00', '18:00'
    ]);

    $reserved = DB::table('reservations')
        ->where('table_number', $tableNumber)
        ->whereDate('reservation_time', $date)
        ->pluck('reservation_time')
        ->map(fn($r) => \Carbon\Carbon::parse($r)->format('H:i'));

    $available = $allSlots->reject(fn($time) => $reserved->contains($time))->values();

    return response()->json($available);
}


    


}

   
    
    

