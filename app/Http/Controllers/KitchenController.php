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

        $reservations = DB::table('order_details')
            ->join('customers', 'order_details.customer_id', '=', 'customers.id')
            ->join('reservations', 'order_details.reservation_id', '=', 'reservations.id')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->select(
                'order_details.id as order_id',
                'order_details.quantity',
                'order_details.notes as order_notes',
                'customers.name as customer_name',
                'reservations.id as reservation_id',
                'reservations.table_id',
                'reservations.pax',
                'reservations.reservation_time',
                'menu.menu_item'
            )
            ->whereDate('reservations.reservation_time', $today)
            ->orderBy('reservations.reservation_time')
            ->get();
        return view('kitchen.home', compact('stock', 'reservations'));
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
