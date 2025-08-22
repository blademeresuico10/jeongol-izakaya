<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashierController extends Controller
{
    public function home()
    {
        $now = Carbon::now();

        $tables = DB::table('tables')->get();
        $menuItems = DB::table('menu')->get();

        $reservations = DB::table('reservations')
            ->whereDate('reservation_time', $now->toDateString())
            ->where('reservation_time', '<=', $now)
            ->where('reservation_end_time', '>=', $now)
            ->where('status', 'Accepted')
            ->get();

        $reservationIds = $reservations->pluck('id')->toArray();
        $orders = DB::table('order_details')
            ->whereIn('reservation_id', $reservationIds)
            ->get()
            ->groupBy('reservation_id'); 

        $occupiedTables = [];
        foreach ($tables as $table) {
            $res = $reservations->firstWhere('table_id', $table->id);

            if ($res) {
                $table->current_reservation_id = $res->id;

                $endTime = Carbon::parse($res->reservation_end_time);
                $table->remaining_seconds = $endTime->lessThanOrEqualTo($now)
                    ? 0
                    : $now->diffInSeconds($endTime);

                $table->current_orders = $orders[$res->id] ?? [];
                $occupiedTables[] = $table->id;
            } else {
                $table->current_reservation_id = null;
                $table->remaining_seconds = null;
                $table->current_orders = [];
            }
        }
        $menuPricesMap = [];
        foreach ($menuItems as $item) {
            $baseName = str_replace([' Lunch', ' Dinner'], '', $item->menu_item);
            if (!isset($menuPricesMap[$baseName])) {
                $menuPricesMap[$baseName] = ['lunch' => null, 'dinner' => null];
            }

            if (str_contains($item->menu_item, 'Lunch')) {
                $menuPricesMap[$baseName]['lunch'] = $item->price;
            } elseif (str_contains($item->menu_item, 'Dinner')) {
                $menuPricesMap[$baseName]['dinner'] = $item->price;
            } else {
                $menuPricesMap[$baseName]['lunch'] = $item->price;
                $menuPricesMap[$baseName]['dinner'] = $item->price;
            }
        }
        $groupedMenu = [];
        foreach ($menuItems as $item) {
            $groupedMenu[$item->category][] = $item;
        }

        return view('cashier.home', compact(
            'tables',
            'menuItems',
            'reservations',
            'menuPricesMap',
            'groupedMenu',
            'occupiedTables'
        ));
    }

    public function getOrders($reservationId)
    {

        $reservation = DB::table('reservations')
            ->join('customers', 'reservations.customer_id', '=', 'customers.id')
            ->where('reservations.id', $reservationId)
            ->select(
                'reservations.id as reservation_id',
                'customers.name as customer_name',
                'reservations.pax'
            )
            ->first();

        if (!$reservation) {
            return response()->json(null);
        }

        $orders = DB::table('order_details')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->where('order_details.reservation_id', $reservationId)
            ->select(
                'menu.menu_item as order_name',
                'order_details.quantity',
                'order_details.order_price as price'
            )
            ->get();

        return response()->json([
            'reservation_id' => $reservation->reservation_id,
            'customer_name' => $reservation->customer_name,
            'pax' => $reservation->pax,
            'orders' => $orders
        ]);
    }
}
