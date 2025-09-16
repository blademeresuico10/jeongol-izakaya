<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\transaction;
use App\Models\transactionDetail;
use App\Models\reservation;
use App\Models\stock;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportsController extends Controller
{


    public function index()
    {
        return view('admin.reports');
    }

    public function salesReport(Request $request)
    {
        $filter = $request->get('filter');
        $now = now();

        switch ($filter) {
            case 'daily':
                $dateFrom = $now->copy()->startOfDay();
                $dateTo = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $dateFrom = $now->copy()->startOfWeek();
                $dateTo = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $dateFrom = $now->copy()->startOfMonth();
                $dateTo = $now->copy()->endOfMonth();
                break;
            case 'yearly':
                $dateFrom = $now->copy()->startOfYear();
                $dateTo = $now->copy()->endOfYear();
                break;
            default:
                $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : $now->copy()->startOfMonth();
                $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : $now->copy()->endOfDay();
                break;
        }

        $transactionsQuery = Transaction::whereBetween('created_at', [$dateFrom, $dateTo]);

        $totalSales = $transactionsQuery->sum('total');
        $transactionCount = $transactionsQuery->count();
        $averageOrderValue = $transactionsQuery->avg('total');

        $transactionIds = $transactionsQuery->pluck('id');

        $topItems = TransactionDetail::select('item_name', DB::raw('SUM(quantity) as total_quantity'))
            ->whereIn('transaction_id', $transactionIds)
            ->groupBy('item_name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        return response()->json([
            'totalSales' => $totalSales,
            'transactionCount' => $transactionCount,
            'averageOrderValue' => $averageOrderValue,
            'topSellingItems' => $topItems,
        ]);
    }


    public function revenueReport(Request $request)
    {
        $filter = $request->get('filter');
        $now = now();

        switch ($filter) {
            case 'daily':
                $dateFrom = $now->copy()->startOfDay();
                $dateTo = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $dateFrom = $now->copy()->startOfWeek();
                $dateTo = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $dateFrom = $now->copy()->startOfMonth();
                $dateTo = $now->copy()->endOfMonth();
                break;
            case 'yearly':
                $dateFrom = $now->copy()->startOfYear();
                $dateTo = $now->copy()->endOfYear();
                break;
            default:
                $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : $now->copy()->startOfMonth();
                $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : $now->copy()->endOfDay();
                break;
        }

        // Overall totals
        $transactionsQuery = Transaction::whereBetween('created_at', [$dateFrom, $dateTo]);
        $gross = $transactionsQuery->sum('total');
        $discounts = $transactionsQuery->sum('discount_total');
        $advance = $transactionsQuery->sum('advance_payment');
        $net = $gross - $discounts;

        $transactionIds = $transactionsQuery->pluck('id');

        $byCategory = DB::table('transaction_details as td')
            ->join('order_details as od', 'td.order_detail_id', '=', 'od.id')
            ->join('menu as m', 'od.menu_id', '=', 'm.id')
            ->whereIn('td.transaction_id', $transactionIds)
            ->select(
                'm.category',
                DB::raw('SUM(od.order_price * od.quantity) as revenue'),
                DB::raw('SUM(od.quantity) as items_sold')
            )
            ->groupBy('m.category')
            ->get();

        return response()->json([
            'grossRevenue'   => round($gross, 2),
            'discounts'      => round($discounts, 2),
            'advancePayments' => round($advance, 2),
            'netRevenue'     => round($net, 2),
            'byCategory'     => $byCategory
        ]);
    }


    public function reservationReport(Request $request)
    {
        $filter = $request->get('filter');
        $now = now();

        switch ($filter) {
            case 'daily':
                $dateFrom = $now->copy()->startOfDay();
                $dateTo = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $dateFrom = $now->copy()->startOfWeek();
                $dateTo = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $dateFrom = $now->copy()->startOfMonth();
                $dateTo = $now->copy()->endOfMonth();
                break;
            case 'yearly':
                $dateFrom = $now->copy()->startOfYear();
                $dateTo = $now->copy()->endOfYear();
                break;
            default:
                $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : $now->copy()->startOfMonth();
                $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : $now->copy()->endOfDay();
                break;
        }

        $reservationsQuery = Reservation::whereBetween('created_at', [$dateFrom, $dateTo]);

        $statusCounts = $reservationsQuery
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(pax) as total_pax'))
            ->groupBy('status')
            ->get();

        $rejectedCount = $statusCounts->where('status', 'Rejected')->first();
        $rejectedCountValue = $rejectedCount ? $rejectedCount->count : 0;

        return response()->json([
            'totalReservations' => $reservationsQuery->count(),
            'totalPax'          => $reservationsQuery->sum('pax'),
            'statusCounts'      => $statusCounts,
            'rejectedReservations' => $rejectedCountValue,
            'topTables'         => $reservationsQuery
                ->select('table_id', DB::raw('COUNT(*) as count'))
                ->groupBy('table_id')
                ->orderByDesc('count')
                ->take(5)
                ->get(),
        ]);
    }



    public function staffReport(Request $request)
{
    try {
        $filter = $request->get('filter', 'daily'); // default to daily
        $query = Transaction::query();

        // Apply date filter
        switch ($filter) {
            case 'daily':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'monthly':
                $query->whereYear('created_at', Carbon::now()->year)
                      ->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'yearly':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
        }

        // Aggregate transactions by cashier
        $transactions = $query->select(
            'cashier_id',
            DB::raw('COUNT(*) as transactions'),
            DB::raw('SUM(total) as total_sales')
        )
        ->groupBy('cashier_id')
        ->get();

        // Map cashier names
        $cashierIds = $transactions->pluck('cashier_id')->toArray();
        $cashiersMap = User::whereIn('id', $cashierIds)
            ->select('id', DB::raw("CONCAT(firstname, ' ', lastname) as full_name"))
            ->pluck('full_name', 'id')
            ->toArray();

        $cashiers = $transactions->map(function ($row) use ($cashiersMap) {
            return [
                'cashier_name'   => $cashiersMap[$row->cashier_id] ?? 'N/A',
                'transactions'   => $row->transactions,
                'total_sales'    => $row->total_sales,
                'avg_transaction' => $row->transactions > 0
                    ? round($row->total_sales / $row->transactions, 2)
                    : 0,
            ];
        });

        return response()->json(['cashierPerformance' => $cashiers]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to generate staff report'
        ], 500);
    }
}


    public function stockReport()
    {
        $stocks = Stock::all();

        $stockData = $stocks->map(function ($stock) {
            return [
                'stock_name' => $stock->stock_name,
                'initial_stock' => $stock->stock_quantity,
                'used_today' => 0,
                'remaining_stock' => $stock->stock_quantity,
                'updated_at' => $stock->updated_at,
            ];
        });

        return response()->json(['stockData' => $stockData]);
    }
}
