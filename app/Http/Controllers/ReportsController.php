<?php

namespace App\Http\Controllers;

use App\Models\ingredients;
use Illuminate\Http\Request;
use App\Models\transaction;
use App\Models\transactionDetail;
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
        $filter = $request->query('filter', 'daily');

        switch ($filter) {
            case 'weekly':
                $dateFrom = now()->startOfWeek();
                $dateTo = now()->endOfWeek();
                break;
            case 'monthly':
                $dateFrom = now()->startOfMonth();
                $dateTo = now()->endOfMonth();
                break;
            case 'yearly':
                $dateFrom = now()->startOfYear();
                $dateTo = now()->endOfYear();
                break;
            default:
                $dateFrom = now()->startOfDay();
                $dateTo = now()->endOfDay();
                break;
        }

        $sales = transaction::with(['customer', 'reservation', 'walkin', 'cashier'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        $totalSales = $sales->sum('grand_total');
        $totalDiscounts = $sales->sum('discount_total');
        $transactionCount = $sales->count();
        $totalPax = DB::table('reservations')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('pax');

        return view('reports.pdf-sales', compact(
            'filter',
            'sales',
            'totalSales',
            'totalDiscounts',
            'transactionCount',
            'totalPax',
            'dateFrom',
            'dateTo'
        ));
    }

    public function transactionReport(Request $request)
    {
        try {
            $filter = $request->get('filter', 'daily');

            $query = DB::table('transactions')
                ->join('users', 'transactions.cashier_id', '=', 'users.id')
                ->select(
                    DB::raw("CONCAT(users.firstname, ' ', users.lastname) as cashier_name"),
                    DB::raw('COUNT(transactions.id) as transactions'),
                    DB::raw('SUM(transactions.total) as total_sales')
                )
                ->groupBy('transactions.cashier_id', 'users.firstname', 'users.lastname');

            switch ($filter) {
                case 'daily':
                    $query->whereDate('transactions.created_at', now());
                    break;
                case 'weekly':
                    $query->whereBetween('transactions.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'monthly':
                    $query->whereYear('transactions.created_at', now()->year)
                        ->whereMonth('transactions.created_at', now()->month);
                    break;
                case 'yearly':
                    $query->whereYear('transactions.created_at', now()->year);
                    break;
            }

            $cashiers = $query->get()->map(function ($row) {
                return [
                    'cashier_name'    => $row->cashier_name,
                    'transactions'    => $row->transactions,
                    'total_sales'     => $row->total_sales,
                    'avg_transaction' => $row->transactions > 0
                        ? round($row->total_sales / $row->transactions, 2)
                        : 0,
                ];
            });

            return response()->json(['cashierPerformance' => $cashiers]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate staff report'], 500);
        }
    }

    public function stockReport()
    {
        $stocks = ingredients::all();

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
