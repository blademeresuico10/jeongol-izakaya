<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\reservation;
use App\Models\User;
use App\Models\reservationPayment;
use App\Notifications\ReservationPaid;
use Illuminate\Support\Facades\Notification;
use App\Models\orders;
use App\Models\menu;
use App\Models\walkin;
use App\Models\table;

class CustomerController extends Controller
{
    public function index()
    {
        $mainMenuItems = menu::where('category', 'main')
            ->where('status', '!=', 'Blocked')
            ->take(3)
            ->get();

        return view('customer.index', compact('mainMenuItems'));
    }

    public function place_reservation(Request $request)
    {
        $tables = DB::table('tables')->get();
        $reservations = DB::table('reservations')
            ->whereDate('started_at', Carbon::now()->toDateString())
            ->get();

        $menuItems = DB::table('menu')->get()->map(function ($item) {
            $processedItem = clone $item;
            $processedItem->display_name = $item->display_name ?? $item->menu_item;
            $processedItem->price = $item->regular_price;

            return $processedItem;
        });

        $groupedMenu = $menuItems->groupBy('category');

        return view('customer.place_reservation', compact(
            'tables',
            'reservations',
            'groupedMenu',
            'menuItems'
        ));
    }

    public function checkAvailability(Request $request)
    {
        $date = $request->input('date');
        $time = $request->input('time');

        if (!$date || !$time) {
            return response()->json(['tables' => []]);
        }

        $searchDateTime = \Carbon\Carbon::parse("$date $time");
        $now = \Carbon\Carbon::now();

        $tables = table::all();
        $availabilityData = [];

        foreach ($tables as $table) {
            $isCurrentlyOccupied = reservation::where('table_id', $table->id)
                ->where('status', 'Active')
                ->where('started_at', '<=', $now)
                ->where('ended_at', '>=', $now)
                ->exists();

            $isActiveReservation = reservation::where('table_id', $table->id)
                ->where('status', 'Active')
                ->where('started_at', '<=', $searchDateTime)
                ->where('ended_at', '>=', $searchDateTime)
                ->exists();

            $isPendingReservation = reservation::where('table_id', $table->id)
                ->where('status', 'Pending')
                ->where('started_at', '<=', $searchDateTime)
                ->where('ended_at', '>=', $searchDateTime)
                ->exists();

            $isBookedWalkin = walkin::where('table_id', $table->id)
                ->where('status', 'active')
                ->where('started_at', '<=', $searchDateTime)
                ->where('ended_at', '>=', $searchDateTime)
                ->exists();

            $availabilityData[] = [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'is_available' => !($isActiveReservation || $isBookedWalkin || $isPendingReservation),
                'is_pending' => $isPendingReservation,
                'is_active' => $isActiveReservation || $isBookedWalkin,
                'is_currently_occupied' => $isCurrentlyOccupied 
            ];
        }

        return response()->json(['tables' => $availabilityData]);
    }

    public function checkOperatingHours(Request $request)
    {
        $date = $request->input('date');
        $time = $request->input('time');

        $checkDate = $date ?: Carbon::today()->format('Y-m-d');

        $operatingHours = DB::table('operating_hours')
            ->where('date', $checkDate)
            ->first();

        if (!$operatingHours) {
            $operatingHours = DB::table('operating_hours')
                ->where('is_default', true)
                ->first();
        }

        if (!$operatingHours) {
            return response()->json([
                'is_open' => false,
                'message' => 'Operating hours are not set. Please contact us for information.',
                'open_time' => 'Not Set',
                'close_time' => 'Not Set'
            ]);
        }

        if ($operatingHours->is_closed) {
            return response()->json([
                'is_open' => false,
                'message' => 'We are closed on this date.',
                'open_time' => 'Closed',
                'close_time' => 'Closed'
            ]);
        }

        $openTimeFormatted = Carbon::parse($operatingHours->open_time)->format('g:i A');
        $closeTimeFormatted = Carbon::parse($operatingHours->close_time)->format('g:i A');

        if (!$time) {
            return response()->json([
                'is_open' => false,
                'message' => 'Please select both date and time.',
                'open_time' => $openTimeFormatted,
                'close_time' => $closeTimeFormatted
            ]);
        }

        $selectedDateTime = Carbon::parse("$date $time");
        $openTime = Carbon::parse("$date " . $operatingHours->open_time);
        $closeTime = Carbon::parse("$date " . $operatingHours->close_time);

        if ($closeTime->lessThan($openTime)) {
            $closeTime->addDay();
            if ($selectedDateTime->format('H:i') < $openTime->format('H:i')) {
                $selectedDateTime->addDay();
            }
        }

        if ($selectedDateTime->between($openTime, $closeTime)) {
            return response()->json([
                'is_open' => true,
                'message' => '',
                'open_time' => $openTimeFormatted,
                'close_time' => $closeTimeFormatted
            ]);
        } else {
            return response()->json([
                'is_open' => false,
                'message' => sprintf(
                    'Selected time is outside operating hours (%s - %s).',
                    $openTimeFormatted,
                    $closeTimeFormatted
                ),
                'open_time' => $openTimeFormatted,
                'close_time' => $closeTimeFormatted
            ]);
        }
    }


    public function storeReservation(Request $request)
    {
        $validator = validator($request->all(), [
            'table_id'           => 'required|exists:tables,id',
            'customer_name'      => 'required|string|max:255',
            'pax'                => 'required|integer|min:1',
            'reserved_date'      => 'required|date',
            'arrival_time'       => 'required|date_format:H:i',
            'advance_payment'    => 'required|numeric|min:1',
            'payment_method'     => 'required|in:gcash,maya,cash',
            'email'              => 'required|email|max:255',
            'payment_proof'      => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'ewallet_id'         => 'nullable|exists:ewallet_details,id',
            'registered_number'  => 'required_if:payment_method,gcash,maya|string',
            'registered_name'    => 'required_if:payment_method,gcash,maya|string',
            'orders'             => 'required|array|min:1',
            'orders.*.menu_id'   => 'required|exists:menu,id',
            'orders.*.quantity'  => 'required|integer|min:1',
            'orders.*.notes'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        return DB::transaction(function () use ($validator, $request) {
            try {
                $validated = $validator->validated();
                $userId = Auth::id();

                $table = DB::table('tables')
                    ->where('id', $validated['table_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$table) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Table not found.'
                    ], 404);
                }

                $reservationTime    = Carbon::parse($validated['reserved_date'] . ' ' . $validated['arrival_time']);
                $reservationEndTime = $reservationTime->copy()->addHours(2);

                if ($reservationTime->lt(now())) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot make reservation in the past.'
                    ], 400);
                }

                $conflict = DB::table('reservations')
                    ->where('table_id', $table->id)
                    ->whereIn('status', ['Pending', 'Accepted'])
                    ->where(function ($query) use ($reservationTime, $reservationEndTime) {
                        $query->where('started_at', '<', $reservationEndTime)
                            ->where('ended_at', '>', $reservationTime);
                    })
                    ->exists();

                if ($conflict) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The time you selected is already reserved.'
                    ], 409);
                }

                $customer = DB::table('customers')
                    ->where('name', $validated['customer_name'])
                    ->where('email', $validated['email'])
                    ->first();

                if (!$customer) {
                    $customerId = DB::table('customers')->insertGetId([
                        'name'           => $validated['customer_name'],
                        'contact_number' => $validated['registered_number'] ?? null,
                        'email'          => $validated['email'] ?? null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                } else {
                    $customerId = $customer->id;
                }

                $reservation = Reservation::create([
                    'pax'         => $validated['pax'],
                    'started_at'  => $reservationTime,
                    'ended_at'    => $reservationEndTime,
                    'status'      => 'Pending',
                    'table_id'    => $table->id,
                    'customer_id' => $customerId,
                    'user_id'     => $userId,
                ]);

                $paymentProofPath = null;
                if ($request->hasFile('payment_proof')) {
                    $paymentProofPath = $request->file('payment_proof')
                        ->store('payment_proofs', 'public');
                }

                reservationPayment::create([
                    'registered_name'                   => $validated['registered_name'],
                    'registered_number'      => $validated['registered_number'],
                    'advance_payment'        => $validated['advance_payment'],
                    'payment_method'         => $validated['payment_method'],
                    'payment_proof'          => $paymentProofPath,
                    'reservation_id'         => $reservation->id,
                    'ewallet_id'             => $validated['ewallet_id'],
                ]);

                foreach ($validated['orders'] as $order) {
                    $menu = DB::table('menu')->find($order['menu_id']);

                    orders::create([
                        'reservation_id' => $reservation->id,
                        'menu_id'        => $menu->id,
                        'quantity'       => $order['quantity'],
                        'price'          => $menu->regular_price,
                        'notes'          => $order['notes'] ?? null,
                        'status'         => 'Pending',
                    ]);
                }

                $this->notifyReceptionists($reservation, $validated['customer_name']);

                return response()->json([
                    'success' => true,
                    'message' => 'Reservation created successfully.',
                    'reservation_id' => $reservation->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Error in storeReservation', [
                    'message' => $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create reservation.',
                ], 500);
            }
        });
    }


    private function notifyReceptionists($reservation, $customerName)
    {
        try {
            if (!$reservation || !$reservation->id) {
                Log::error('Cannot notify: Invalid reservation object');
                return;
            }

            $receptionists = User::where('role', 'receptionist')->get();

            if ($receptionists->isEmpty()) {
                Log::warning('No receptionists found to notify for reservation: ' . $reservation->id);
                return;
            }

            $notifications = [];
            foreach ($receptionists as $receptionist) {
                $notifications[] = [
                    'message' => "New reservation request from {$customerName}.",
                    'is_read' => false,
                    'user_id' => $receptionist->id,
                    'reservation_id' => $reservation->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($notifications)) {
                DB::table('reservation_notifications')->insert($notifications);
            }
        } catch (\Exception $e) {
            Log::error('Error notifying receptionists: ' . $e->getMessage());
        }
    }

    public function getUnavailableTimes(Request $request)
    {
        $tableId = $request->get('table_id');

        if (!$tableId) {
            return response()->json([]);
        }

        $reservations = DB::table('reservations')
            ->where('table_id', $tableId)
            ->whereDate('started_at', Carbon::now()->toDateString())
            ->whereIn('status', ['Pending', 'Active'])
            ->select('id', 'started_at', 'ended_at', 'table_id', DB::raw("'reservation' as source"))
            ->get();

        $walkIns = DB::table('walk_ins')
            ->where('table_id', $tableId)
            ->whereDate('started_at', Carbon::now()->toDateString())
            ->where('status', 'Active')
            ->select('id', 'started_at', 'ended_at', 'table_id', DB::raw("'walkin' as source"))
            ->get();

        $combined = $reservations->merge($walkIns)->sortBy('started_at')->values();

        return response()->json($combined);
    }



    public function storeFeedback(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        try {
            DB::table('feedback')->insert([
                'message' => $validated['message'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Feedback submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Feedback Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Feedback submission failed. Please try again.');
        }
    }
}
