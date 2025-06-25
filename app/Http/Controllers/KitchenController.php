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

        $today = Carbon::today()->toDateString(); // e.g. "2025-06-23"

        $reservations = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->leftJoin('order_details', 'reservations.id', '=', 'order_details.reservation_id')
            ->leftJoin('menu', 'order_details.menu_id', '=', 'menu.id')
            ->select(
                'reservations.id as reservation_id',
                'reservations.table_number',
                'reservations.pax',
                'reservations.notes',
                'reservations.reservation_time',
                'customers.name as customer_name',
                'menu.menu_item',
                'order_details.quantity'
            )
            ->whereDate('reservations.reservation_time', $today)
            ->orderBy('reservations.reservation_time') // FIFO
            ->get();

        return view('kitchen.home', [
            'stock' => $stock,
            'reservations' => $reservations
        ]);
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
