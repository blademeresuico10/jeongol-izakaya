<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\customers;
use Illuminate\Support\Facades\Auth;


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

        // Get current time and today's reservations
        $now = Carbon::now();
        $reservations = DB::table('reservations')
            ->whereDate('reservation_time', $now->toDateString())
            ->get();

        return view('customer.place_reservation', compact('tables', 'menuItems'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $userId = Auth::id();

            // 1. Check if customer exists
            $existing = DB::table('customers')->where('name', $data['customer_name'])->first();
            if (!$existing) {
                $customerId = DB::table('customers')->insertGetId([
                    'name' => $data['customer_name'],
                    'contact_number' => $data['contact_number'],
                    'id_type' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $customerId = $existing->id;
            }

            // 2. Count pax
            $totalPax = collect($data['menu'])->sum('quantity');

            // 3. Combine date & time and determine if lunch or dinner
            $reservationDateTime = Carbon::parse($data['reserved_date'] . ' ' . $data['arrival_time']);
            $isLunch = $reservationDateTime->hour <= 12;

            // 4. Create reservation
            // 4. Check for reservation conflicts
            $startTime = $reservationDateTime;
            $endTime = $reservationDateTime->copy()->addHours(2);

            $conflict = DB::table('reservations')
                ->where('table_number', $data['table_number'])
                ->whereDate('reservation_time', $startTime->toDateString())
                ->get()
                ->filter(function ($existing) use ($startTime, $endTime) {
                    $existingStart = Carbon::parse($existing->reservation_time);
                    $existingEnd = $existingStart->copy()->addHours(2);
                    return $startTime < $existingEnd && $endTime > $existingStart;
                })
                ->isNotEmpty();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'This table is already reserved during the selected time slot.',
                ]);
            }

            // 5. Create reservation
            $reservationId = DB::table('reservations')->insertGetId([
                'pax' => $totalPax,
                'reservation_time' => $reservationDateTime,
                'advance_payment' => 0.00,
                'table_number' => $data['table_number'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'customer_id' => $customerId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            
            // 5. Store order_details with correct price
        foreach ($data['menu'] as $item) {
            $itemName = $item['item'];
            $quantity = (int) $item['quantity'];

            if ($quantity < 1) continue; // ✅ Skip invalid/zero quantity

            // Determine menu variant
            $menuKey = match ($itemName) {
                'Samgyupsal' => $isLunch ? 'Samgyup Lunch' : 'Samgyup Dinner',
                'Hotpot'     => $isLunch ? 'HotPot Lunch' : 'HotPot Dinner',
                default      => $itemName,
            };

            $menu = DB::table('menu')->where('menu_item', $menuKey)->first();

            if (!$menu) continue;

            $orderPrice = $menu->price * $quantity;

            DB::table('order_details')->insert([
                'order_price'   => $orderPrice,
                'quantity'      => $quantity,
                'customer_id'   => $customerId,
                'user_id'       => $userId,
                'menu_id'       => $menu->id,
                'reservation_id'=> $reservationId,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
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



}
