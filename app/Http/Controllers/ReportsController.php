<?php

namespace App\Http\Controllers;

use App\Models\ingredients;
use App\Models\ingredientBatch;
use App\Models\ingredientMovements;
use App\Models\expiredIngredients;
use Illuminate\Http\Request;
use App\Models\transaction;
use App\Models\transactionDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\orders;

class ReportsController extends Controller
{


    public function index()
    {
        return view('admin.reports');
    }




    public function salesReportPdf(Request $request)
    {
        $filter = $request->query('filter');

        switch ($filter) {
            case 'weekly':
                $dateFrom = now()->startOfWeek();
                $dateTo = now()->endOfWeek();
                $filterDate = 'As of This Week';
                break;
            case 'monthly':
                $dateFrom = now()->startOfMonth();
                $dateTo = now()->endOfMonth();
                $filterDate = 'As of This Month';
                break;
            case 'yearly':
                $dateFrom = now()->startOfYear();
                $dateTo = now()->endOfYear();
                $filterDate = 'As of This Year';
                break;
            default:
                $dateFrom = now()->startOfDay();
                $dateTo = now()->endOfDay();
                $filterDate = 'As of Today';
                break;
        }

        $transactions = transaction::with(['transactionDetails.orders', 'reservation', 'walkin'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        $grossSales = $transactions->sum('orders_total');
        $netSales = $transactions->sum('grand_total');
        $totalDiscounts = $transactions->sum('discount_total');
        $totalCustomers = $transactions->sum(fn($t) => $t->reservation->pax ?? $t->walkin->pax ?? 0);

        $allDetails = [];
        foreach ($transactions as $t) {
            foreach ($t->transactionDetails as $d) {
                $price = $d->orders->price ?? 0;
                $quantity = $d->quantity ?? 0;
                $discount = $d->discount_amount ?? 0;
                $computedTotal = max(0, ($price * $quantity) - abs($discount));


                $allDetails[] = [
                    'item_name' => $d->item_name,
                    'quantity' => $d->quantity,
                    'total' => $computedTotal,
                ];
            }
        }

        $groupedSales = collect($allDetails)
            ->groupBy('item_name')
            ->map(fn($items, $name) => [
                'item_name' => $name,
                'quantity' => $items->sum('quantity'),
                'total' => $items->sum('total'),
            ])
            ->values();

        $pdf = Pdf::loadView('admin.reports.pdf-sales', [
            'groupedSales' => $groupedSales,
            'grossSales' => $grossSales,
            'netSales' => $netSales,
            'totalDiscounts' => $totalDiscounts,
            'totalCustomers' => $totalCustomers,
            'filterDate' => $filterDate,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Sales_Report_' . now()->format('Ymd_His') . '.pdf');
    }


    public function transactionReport(Request $request)
    {
        $filter = $request->query('filter');

        switch ($filter) {
            case 'daily':
            case 'today':
                $dateFrom = now()->startOfDay();
                $dateTo = now()->endOfDay();
                break;

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
                $dateFrom = now()->startOfMonth();
                $dateTo = now()->endOfMonth();
                break;
        }

        $transactions = transaction::with([
            'cashier',
            'customer',
            'walkin',
            'reservation',
            'transactionDetails',
        ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get()
            ->map(function ($t) {
                $pax = 0;
                if ($t->reservation) {
                    $pax = $t->reservation->pax;
                } elseif ($t->walkin) {
                    $pax = $t->walkin->pax;
                }

                return (object) [
                    'transaction_no' => $t->transaction_no,
                    'date' => $t->created_at->format('M d, Y g:i A'),
                    'staff_name' => $t->cashier ? trim($t->cashier->firstname . ' ' . $t->cashier->lastname) : '[Deleted User]',
                    'customer_name' => $t->customer?->name ?? 'N/A',
                    'payment_method' => ucfirst($t->payment_method ?? 'Cash'),
                    'total_amount' => $t->grand_total,
                    'pax' => $pax,
                ];
            });

        $groupedTransactions = $transactions->groupBy('staff_name');

        $pdf = PDF::loadView('admin.reports.pdf-transaction', [
            'groupedTransactions' => $groupedTransactions,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now(),
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Transaction_Report_' . now()->format('Ymd_His') . '.pdf');
    }


    public function stockReport(Request $request)
{
    try {
        $filter = $request->query('filter');

        switch ($filter) {
            case 'daily':
            case 'today':
                $dateFrom = now()->startOfDay();
                $dateTo = now()->endOfDay();
                break;

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
                $dateFrom = now()->startOfMonth();
                $dateTo = now()->endOfMonth();
                break;
        }

        // Get current stocks (all ingredients) - removed eager loading
        $currentStocks = ingredients::all();

        // Stock IN (arrivals) - ingredient batches that arrived in date range
        $stockIns = ingredientBatch::with('ingredient')
            ->whereBetween('arrived_at', [$dateFrom, $dateTo])
            ->get();

        // Consumed stocks (used) - ingredient movements that were used
        $consumedStocks = ingredientMovements::with(['ingredient', 'order'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('type', 'used')
            ->get();

        // Expired stocks
        $expiredStocks = expiredIngredients::with(['ingredient', 'ingredientBatch'])
            ->whereBetween('expired_at', [$dateFrom, $dateTo])
            ->get();

        $pdf = PDF::loadView('admin.reports.pdf-stock', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'generatedAt' => now(),
            'currentStocks' => $currentStocks,
            'consumedStocks' => $consumedStocks,
            'stockIns' => $stockIns,
            'expiredStocks' => $expiredStocks,
        ])->setPaper('A4', 'portrait');

        return $pdf->download('Stocks_Report_' . now()->format('Ymd_His') . '.pdf');
        
    } catch (\Exception $e) {
        return response()->make("
            <script>
                console.error('=== STOCK REPORT ERROR ===');
                console.error('Message: " . addslashes($e->getMessage()) . "');
                console.error('File: " . addslashes($e->getFile()) . "');
                console.error('Line: " . $e->getLine() . "');
                console.error('=== END ERROR ===');
            </script>
            <h1>Error occurred - Check browser console (F12)</h1>
            <p><strong>Message:</strong> {$e->getMessage()}</p>
            <p><strong>File:</strong> {$e->getFile()}</p>
            <p><strong>Line:</strong> {$e->getLine()}</p>
        ", 500);
    }
}
}
