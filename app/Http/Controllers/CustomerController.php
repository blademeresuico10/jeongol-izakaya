<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\reservation;
use App\Models\User;


class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.index');
    }

    public function place_reservation(Request $request)
    {
        $tables = DB::table('tables')->get();
        $reservations = DB::table('reservations')
            ->whereDate('reservation_time', Carbon::now()->toDateString())
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


    public function storeReservation(Request $request)
    {
        $validator = validator($request->all(), [
            'table_id'           => 'required|exists:tables,id',
            'customer_name'      => 'required|string|max:255',
            'contact_number'     => 'required|string|max:15',
            'pax'                => 'required|integer|min:1',
            'reserved_date'      => 'required|date',
            'arrival_time'       => 'required|date_format:H:i',
            'advance_payment'    => 'required|numeric|min:0',
            'payment_method'     => 'required|in:gcash,maya',
            'proof'              => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'ewallet_number_id'  => 'required|exists:ewallet_details,id',
            'number'             => 'required|string',
            'registered_name'    => 'required|string',
            'orders'            => 'required|array|min:1',
            'orders.*.menu_id'  => 'required|exists:menu,id',
            'orders.*.quantity' => 'required|integer|min:1',
            'orders.*.notes'    => 'nullable|string',
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

                $reservationTime = Carbon::parse($validated['reserved_date'] . ' ' . $validated['arrival_time']);
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
                        $query->where('reservation_time', '<', $reservationEndTime)
                            ->where('reservation_end_time', '>', $reservationTime);
                    })
                    ->exists();

                if ($conflict) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Time slot is already reserved.'
                    ], 409);
                }

                $customer = DB::table('customers')
                    ->where('name', $validated['customer_name'])
                    ->where('contact_number', $validated['contact_number'])
                    ->first();

                if (!$customer) {
                    $customerId = DB::table('customers')->insertGetId([
                        'name'           => $validated['customer_name'],
                        'contact_number' => $validated['contact_number'],
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                } else {
                    $customerId = $customer->id;
                }

                $reservation = Reservation::create([
                    'pax'                  => $validated['pax'],
                    'reservation_time'     => $reservationTime,
                    'reservation_end_time' => $reservationEndTime,
                    'status'               => 'Pending',
                    'table_id'             => $table->id,
                    'customer_id'          => $customerId,
                    'user_id'              => $userId,
                ]);

                $paymentProofPath = null;
                if ($request->hasFile('proof')) {
                    $paymentProofPath = $request->file('proof')
                        ->store('payment_proofs', 'public');
                }

                DB::table('reservation_payment_details')->insert([
                    'name'             => $validated['registered_name'],
                    'contact'          => $validated['contact_number'],
                    'advance_payment'  => $validated['advance_payment'],
                    'payment_method'   => $validated['payment_method'],
                    'payment_proof'    => $paymentProofPath,
                    'reservation_id'   => $reservation->id,
                    'ewallet_number'   => $validated['ewallet_number_id'],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                if (!empty($validated['orders'])) {
                    $orderInserts = [];
                    foreach ($validated['orders'] as $order) {
                        $menu = DB::table('menu')->find($order['menu_id']);
                        if ($menu) {
                            $orderInserts[] = [
                                'order_price'    => $menu->regular_price * $order['quantity'],
                                'reservation_id' => $reservation->id,
                                'menu_id'        => $menu->id,
                                'quantity'       => $order['quantity'],
                                'notes'          => $order['notes'] ?? null,
                                'status'         => 'Pending',
                                'customer_id'    => $customerId,
                                'user_id'        => $userId,
                                'created_at'     => now(),
                                'updated_at'     => now(),
                            ];
                        }
                    }

                    if (!empty($orderInserts)) {
                        DB::table('order_details')->insert($orderInserts);
                    }
                }

                $this->notifyReceptionists($reservation, $validated['customer_name']);

                return response()->json([
                    'success' => true,
                    'message' => 'Reservation created successfully.',
                    'reservation_id' => $reservation->id,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('Database error in storeReservation: ' . $e->getMessage());

                if ($e->getCode() == 23000) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Time slot conflict occurred.',
                    ], 409);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Database error occurred.',
                ], 500);
            } catch (\Exception $e) {
                Log::error('Error in storeReservation: ' . $e->getMessage());

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
            $receptionists = User::where('role', 'receptionist')->get();
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
            Log::error('Error sending notifications to receptionists: ' . $e->getMessage());
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
            ->whereDate('reservation_time', Carbon::now()->toDateString())
            ->whereIn('status', ['Pending', 'Accepted'])
            ->select('id', 'reservation_time', 'reservation_end_time', 'table_id')
            ->orderBy('reservation_time')
            ->get();

        return response()->json($reservations);
    }

    public function storeFeedback(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|max:500',
        ]);

        try {
            DB::table('feedback')->insert([
                'email' => $validated['email'],
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
