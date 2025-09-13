<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KitchenController extends Controller
{
    public function home()
    {
        $stock = DB::table('stock')->get();
        $today = Carbon::today()->toDateString();

        // First get valid reservations (not completed)
        $validReservations = DB::table('reservations')
            ->leftJoin('transactions', 'reservations.id', '=', 'transactions.reservation_id')
            ->whereDate('reservations.reservation_time', $today)
            ->where(function ($query) {
                $query->whereNull('transactions.id')
                    ->orWhere('transactions.status', '!=', 'Completed');
            })
            ->pluck('reservations.id');

        $reservations = DB::table('order_details')
            ->join('customers', 'order_details.customer_id', '=', 'customers.id')
            ->join('reservations', 'order_details.reservation_id', '=', 'reservations.id')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->leftJoin('transactions', 'reservations.id', '=', 'transactions.reservation_id')
            ->select(
                'order_details.id as order_id',
                'order_details.quantity',
                'order_details.notes as order_notes',
                'order_details.created_at as order_created_at',
                'order_details.is_added_order',
                'reservations.id as reservation_id',
                'reservations.table_id',
                'reservations.pax',
                'reservations.reservation_time',
                'menu.menu_item',
                'transactions.status as transaction_status'
            )
            ->where('order_details.status', 'Pending')
            ->whereIn('reservations.id', $validReservations)
            ->orderBy('order_details.created_at', 'desc')
            ->get();

        $reservationGroups = $reservations->groupBy('reservation_id');

        return view('kitchen.home', compact('stock', 'reservations', 'reservationGroups'));
    }
    public function updateStock(Request $request)
    {
        foreach ($request->stocks as $id => $quantity) {
            DB::table('stock')
                ->where('id', $id)
                ->update(['stock_quantity' => $quantity]);
        }

        return redirect()->route('kitchen.home')->with('success', 'Stock levels updated successfully.');
    }
}
