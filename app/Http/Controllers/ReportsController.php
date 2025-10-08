<?php

namespace App\Http\Controllers;

use App\Models\ingredients;
use Illuminate\Http\Request;
use App\Models\transaction;
use App\Models\transactionDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{


    public function index()
    {
        return view('admin.reports');
    }

    public function salesReportPdf(Request $request)
    {
        try {
            $filter = $request->query('filter', 'daily');

            switch ($filter) {
                case 'weekly':
                    $dateFrom = now()->startOfWeek();
                    $dateTo = now()->endOfWeek();
                    $filterDate = $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y');
                    break;
                case 'monthly':
                    $dateFrom = now()->startOfMonth();
                    $dateTo = now()->endOfMonth();
                    $filterDate = $dateFrom->format('F Y');
                    break;
                case 'yearly':
                    $dateFrom = now()->startOfYear();
                    $dateTo = now()->endOfYear();
                    $filterDate = $dateFrom->format('Y');
                    break;
                default:
                    $dateFrom = now()->startOfDay();
                    $dateTo = now()->endOfDay();
                    $filterDate = $dateFrom->format('F j, Y');
                    break;
            }

            $transactions = transaction::with(['customer', 'reservation', 'walkin', 'cashier', 'transactionDetails'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->get();

            $grossSales = $transactions->sum('orders_total') ?? 0;
            $netSales = $transactions->sum('grand_total') ?? 0;
            $totalDiscounts = $transactions->sum('discount_total') ?? 0;

            $totalCustomers = $transactions->sum(function ($transaction) {
                return $transaction->reservation->pax ?? $transaction->walkin->pax ?? 0;
            });

            $allDetails = [];
            foreach ($transactions as $transaction) {
                foreach ($transaction->transactionDetails as $detail) {
                    $allDetails[] = [
                        'item_name' => $detail->item_name,
                        'quantity' => $detail->quantity,
                    ];
                }
            }

            $groupedSales = collect($allDetails)->groupBy('item_name')->map(function ($items, $itemName) {
                return [
                    'item_name' => $itemName,
                    'quantity' => $items->sum('quantity'),
                    'total' => 0,
                ];
            })->values();

            $pdf = Pdf::loadView('admin.reports.pdf-sales', compact(
                'filter',
                'filterDate',
                'groupedSales',
                'grossSales',
                'netSales',
                'totalDiscounts',
                'totalCustomers',
                'dateFrom',
                'dateTo'
            ))->setPaper('a4', 'landscape');

            $filename = 'sales-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
           
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
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
