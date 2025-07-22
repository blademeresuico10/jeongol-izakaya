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
    Log::info('Customer Reservation Request:', $data);

    try {
        $validated = validator($data, [
            'customer_name'   => 'required|string',
            'contact_number'  => 'required|string',
            'reserved_date'   => 'required|date',
            'arrival_time'    => 'required|date_format:H:i',
            'table_number'    => 'required|integer',

            'menu'            => 'nullable|array',
            'menu.*.item'     => 'required|string',
            'menu.*.quantity' => 'required|integer|min:1',
            'menu.*.notes'    => 'nullable|string',

            'notes'           => 'nullable|string',
        ])->validate();

        $userId = Auth::id();

        // Create or retrieve customer
        $customer = DB::table('customers')
            ->where('name', $validated['customer_name'])
            ->where('contact_number', $validated['contact_number'])
            ->first();

        $customerId = $customer
            ? $customer->id
            : DB::table('customers')->insertGetId([
                'name'           => $validated['customer_name'],
                'contact_number' => $validated['contact_number'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

        // Validate time window
        $reservedDateTime = Carbon::parse($validated['reserved_date'] . ' ' . $validated['arrival_time']);
        $endDateTime = $reservedDateTime->copy()->addMinutes(30);

        if ($reservedDateTime->isPast()) {
            return response()->json(['success' => false, 'message' => 'Cannot reserve for a past time.'], 400);
        }

        $arrivalMinutes = $reservedDateTime->hour * 60 + $reservedDateTime->minute;
        if ($arrivalMinutes < 690 || $arrivalMinutes > 1080) { // 11:30 AM = 690, 6:00 PM = 1080
            return response()->json(['success' => false, 'message' => 'Reservation time must be between 11:30 AM and 6:00 PM.'], 400);
        }

        // Check for table conflict
        $conflict = DB::table('reservations')
            ->where('table_number', $validated['table_number'])
            ->where(function ($query) use ($reservedDateTime, $endDateTime) {
                $query->where('reservation_time', '<', $endDateTime)
                      ->where('reservation_end_time', '>', $reservedDateTime);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['success' => false, 'message' => 'This table is already reserved for that time.'], 409);
        }

        // Determine if Lunch or Dinner
        $isLunch = $arrivalMinutes < 960; // 4:00 PM = 960

        $totalPrice = 0;
        $pax = 0;

        
        $validOrders = [];

        if (!empty($validated['menu'])) {
        foreach ($validated['menu'] as $order) {
            $menuQuery = DB::table('menu')->where('menu_item', 'LIKE', $order['item'] . '%');

            if ($isLunch) {
                $menuQuery->where('menu_item', 'LIKE', '%Lunch');
            } else {
                $menuQuery->where('menu_item', 'LIKE', '%Dinner');
            }

            $menu = $menuQuery->first();

            if ($menu) {
                $qty = $order['quantity'];
                $linePrice = $menu->price * $qty;
                $totalPrice += $linePrice;
                $pax += $qty;

                $validOrders[] = [
                    'menu_id'       => $menu->id,
                    'quantity'      => $qty,
                    'order_price'   => $linePrice,
                    'notes'         => $order['notes'] ?? '',
                ];
            }
        }
    }


        // Create reservation
        $reservation = Reservation::create([
            'pax'                  => $pax,
            'advance_payment'      => null,
            'reservation_time'     => $reservedDateTime,
            'reservation_end_time' => $endDateTime,
            'table_number'         => $validated['table_number'],
            'notes'                => $validated['notes'] ?? null,
            'customer_id'          => $customerId,
            'user_id'              => $userId,
            'total_price'          => $totalPrice,
        ]);

        // Insert valid orders (only if notes exist and menu found)
        foreach ($validOrders as $order) {
            // Avoid duplicates: check by reservation_id + menu_id
            $exists = DB::table('order_details')
                ->where('reservation_id', $reservation->id)
                ->where('menu_id', $order['menu_id'])
                ->exists();

            if (!$exists) {
                DB::table('order_details')->insert([
                    'order_price'    => $order['order_price'],
                    'quantity'       => $order['quantity'],
                    'notes'          => $order['notes'],
                    'customer_id'    => $customerId,
                    'user_id'        => $userId,
                    'menu_id'        => $order['menu_id'],
                    'reservation_id' => $reservation->id,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Reservation successful!']);
    } catch (\Exception $e) {
        Log::error('Customer Reservation Error:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Reservation failed.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
