<?php

namespace App\Http\Controllers;

use App\Models\ingredients;
use App\Models\ingredientBatch;
use App\Models\ingredientMovements;
use App\Models\expiredIngredients;
use App\Models\transaction;
use App\Models\transactionDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\orders;
use Illuminate\Http\Request;
use App\Models\reservationPayment;
use App\Models\menu;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports');
    }

    public function getSalesReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $transactions = transaction::where('status', 'Completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalSales = $transactions->sum('grand_total');
        $grossSales = $transactions->sum('orders_total');
        $totalDiscounts = $transactions->sum('discount_total');
        $totalOrders = $transactions->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Get e-wallet payments with breakdown
        $gcashTotal = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('rpd.payment_method', 'gcash')
            ->sum('rpd.advance_payment');

        $mayaTotal = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->where('rpd.payment_method', 'maya')
            ->sum('rpd.advance_payment');

        $ewalletTotal = $gcashTotal + $mayaTotal;

        $dailyBreakdown = transaction::where('status', 'Completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(grand_total) as sales'),
                DB::raw('SUM(orders_total) as gross_sales'),
                DB::raw('SUM(discount_total) as discounts')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d, Y'),
                    'orders' => $item->orders,
                    'sales' => floatval($item->sales),
                    'gross_sales' => floatval($item->gross_sales),
                    'discounts' => floatval($item->discounts),
                ];
            });

        $peakDay = $dailyBreakdown->sortByDesc('sales')->first();

        $periodDays = $startDate->diffInDays($endDate) + 1;
        $previousStart = $startDate->copy()->subDays($periodDays);
        $previousEnd = $startDate->copy()->subDay();

        $previousSales = transaction::where('status', 'Completed')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('grand_total');

        $salesGrowth = $previousSales > 0
            ? round((($totalSales - $previousSales) / $previousSales) * 100, 1)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'gross_sales' => round($grossSales, 2),
                    'total_discounts' => round($totalDiscounts, 2),
                    'net_sales' => round($totalSales, 2),
                    'total_orders' => $totalOrders,
                    'average_order_value' => round($averageOrderValue, 2),
                    'sales_growth' => $salesGrowth,
                    'ewallet_total' => round($ewalletTotal, 2),
                    'gcash_total' => round($gcashTotal, 2),
                    'maya_total' => round($mayaTotal, 2),
                ],
                'daily_breakdown' => $dailyBreakdown,
                'peak_day' => $peakDay,
                'period' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                    'days' => $periodDays,
                ]
            ]
        ]);
    }

    public function getTransactionReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $transactions = transaction::with(['reservation.customer', 'walkin', 'cashier'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'Completed')
            ->get();

        $totalTransactions = $transactions->count();
        $totalAmount = $transactions->sum('grand_total');

        $walkinTransactions = $transactions->filter(fn($t) => is_null($t->reservation_id));
        $reservationTransactions = $transactions->filter(fn($t) => !is_null($t->reservation_id));

        $categorizedData = [
            [
                'category' => 'Walk-in',
                'payment_method' => 'Cash',
                'transaction_count' => $walkinTransactions->count(),
                'total_amount' => $walkinTransactions->sum('grand_total'),
            ],
            [
                'category' => 'Reservation',
                'payment_method' => 'Cash',
                'transaction_count' => $reservationTransactions->count(),
                'total_amount' => $reservationTransactions->sum('grand_total'),
            ],
        ];

        $categorizedData = collect($categorizedData)
            ->filter(fn($item) => $item['transaction_count'] > 0)
            ->values();

        $cashierSummary = $transactions->groupBy(
            fn($t) =>
            $t->cashier
                ? "{$t->cashier->firstname} {$t->cashier->lastname}"
                : 'Unknown Cashier'
        )
            ->map(function ($group, $cashierName) {
                return [
                    'cashier_name' => $cashierName,
                    'transaction_count' => $group->count(),
                    'total_amount' => $group->sum('grand_total'),
                ];
            })
            ->values();



        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_transactions' => $totalTransactions,
                    'total_amount' => round($totalAmount, 2),
                    'date_range' => [
                        'from' => $startDate->format('M d, Y'),
                        'to' => $endDate->format('M d, Y'),
                    ],
                ],
                'categorized_transactions' => $categorizedData,
                'cashier_summary' => $cashierSummary,
            ]
        ]);
    }

    public function getInventoryReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:stock-movement,expired,consumption',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $reportType = $request->report_type;

        switch ($reportType) {
            case 'stock-movement':
                return $this->getStockMovementReport($startDate, $endDate);
            case 'expired':
                return $this->getExpiredItemsReport($startDate, $endDate);
            case 'consumption':
                return $this->getConsumptionReport($startDate, $endDate);
            default:
                return response()->json(['success' => false, 'message' => 'Invalid report type'], 400);
        }
    }

    private function getStockMovementReport($startDate, $endDate)
    {
        try {
            $movements = ingredientMovements::with(['ingredient'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            $stockInQtyKg = $movements->where('type', 'stock_in')
                ->where('ingredient.unit', 'kg')->sum('quantity');

            $stockInQtyPcs = $movements->where('type', 'stock_in')
                ->where('ingredient.unit', 'pcs')->sum('quantity');

            $stockOutQtyKg = $movements->whereIn('type', ['stock_out', 'used', 'expired'])
                ->where('ingredient.unit', 'kg')->sum('quantity');

            $stockOutQtyPcs = $movements->whereIn('type', ['stock_out', 'used', 'expired'])
                ->where('ingredient.unit', 'pcs')->sum('quantity');

            $response = [
                'success' => true,
                'data' => [
                    'report_type' => 'stock-movement',
                    'summary' => [
                        'stock_in_kg' => round($stockInQtyKg, 2),
                        'stock_in_pcs' => round($stockInQtyPcs, 2),
                        'stock_out_kg' => round($stockOutQtyKg, 2),
                        'stock_out_pcs' => round($stockOutQtyPcs, 2),
                    ],
                    'movements' => $movements->map(function ($move) {
                        return [
                            'date' => $move->created_at->format('M d, Y h:i A'),
                            'category' => $move->ingredient->category ?? 'Unknown',
                            'type' => $move->type,
                            'quantity' => round($move->quantity, 2),
                            'unit' => $move->ingredient->unit ?? 'kg',
                        ];
                    })->values(),
                ]
            ];


            return response()->json($response);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate stock movement report',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getExpiredItemsReport($startDate, $endDate)
    {
        $expiredItems = expiredIngredients::with(['ingredientBatch.ingredient'])
            ->whereBetween('expired_at', [$startDate, $endDate])
            ->get();

        $expiringSoonBatches = ingredientBatch::with('ingredient')
            ->where('status', 'active')
            ->whereRaw('DATEDIFF(expiration_date, CURDATE()) BETWEEN 0 AND 7')
            ->get();

        $totalWasteQty = $expiredItems->sum('quantity');
        $totalWasteValue = $expiredItems->sum(function ($item) {
            return $item->quantity * 100;
        });

        $byCategory = $expiredItems->groupBy(function ($item) {
            return $item->ingredientBatch->ingredient->category ?? 'Unknown';
        })->map(function ($items, $category) {
            return [
                'name' => $category,
                'count' => $items->count(),
                'value' => round($items->sum(fn($i) => $i->quantity * 100), 2),
                'icon' => $this->getCategoryIcon($category),
                'color' => $this->getCategoryColor($category),
            ];
        })->values();

        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();

        $thisWeekExpired = expiredIngredients::whereBetween('expired_at', [$thisWeekStart, Carbon::now()])->get();
        $thisMonthExpired = expiredIngredients::whereBetween('expired_at', [$thisMonthStart, Carbon::now()])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'report_type' => 'expired',
                'summary' => [
                    'expired_count' => $expiredItems->count(),
                    'expired_value' => round($totalWasteValue, 2),
                    'expiring_soon_count' => $expiringSoonBatches->count(),
                    'total_waste_qty' => round($totalWasteQty, 2),
                    'total_waste_value' => round($totalWasteValue, 2),
                    'by_category' => $byCategory,
                    'trend' => [
                        'this_week' => $thisWeekExpired->count(),
                        'this_week_value' => round($thisWeekExpired->sum(fn($i) => $i->quantity * 100), 2),
                        'this_month' => $thisMonthExpired->count(),
                        'this_month_value' => round($thisMonthExpired->sum(fn($i) => $i->quantity * 100), 2),
                        'avg_per_week' => round($expiredItems->count() / max(1, $startDate->diffInWeeks($endDate) ?: 1), 1),
                    ],
                ],
                'expired_items' => $expiredItems->map(function ($item) {
                    $expiredDate = Carbon::parse($item->expired_at);
                    $ingredient = $item->ingredientBatch->ingredient ?? null;

                    return [
                        'name' => $ingredient->name ?? 'Unknown',
                        'category' => $ingredient->category ?? 'Unknown',
                        'batch_id' => $item->ingredient_batch_id,
                        'quantity' => round($item->quantity, 2),
                        'unit' => $ingredient->unit ?? 'kg',
                        'expiration_date' => $expiredDate->format('M d, Y'),
                        'days_expired' => Carbon::now()->diffInDays($expiredDate),
                        'value_lost' => round($item->quantity * 100, 2),
                    ];
                })->values(),
                'expiring_soon' => $expiringSoonBatches->map(function ($batch) {
                    $expirationDate = Carbon::parse($batch->expiration_date);
                    return [
                        'name' => $batch->ingredient->name,
                        'category' => $batch->ingredient->category,
                        'expiration_date' => $expirationDate->format('M d, Y'),
                        'days_until_expiry' => Carbon::today()->diffInDays($expirationDate, false),
                    ];
                })->values(),
            ]
        ]);
    }

    private function getConsumptionReport($startDate, $endDate)
    {
        $movements = ingredientMovements::with(['ingredient', 'order'])
            ->where('type', 'used')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalConsumed = $movements->count();
        $totalQuantity = $movements->sum('quantity');
        $totalValue = $movements->sum(fn($m) => $m->quantity * 100);
        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);
        $avgDaily = $totalQuantity / $periodDays;

        // 🔹 Group movements by Date + Category
        $groupedConsumption = $movements
            ->groupBy(function ($move) {
                return Carbon::parse($move->created_at)->format('Y-m-d');
            })
            ->flatMap(function ($dayGroup, $date) {
                return $dayGroup->groupBy(function ($item) {
                    return $item->ingredient->category ?? 'Unknown';
                })->map(function ($catGroup, $category) use ($date) {
                    $qty = $catGroup->sum('quantity');
                    $unit = optional($catGroup->first()->ingredient)->unit ?? 'kg';
                    return [
                        'date' => Carbon::parse($date)->format('M d, Y'),
                        'category' => $category,
                        'quantity' => round($qty, 2),
                        'unit' => $unit,
                    ];
                });
            })
            ->values();

        // 🔹 Top consumed items
        $topConsumed = $movements->groupBy('ingredient_id')
            ->map(function ($items) {
                $ingredient = $items->first()->ingredient;
                return [
                    'name' => $ingredient->name ?? 'Unknown',
                    'category' => $ingredient->category ?? 'Unknown',
                    'total_consumed' => round($items->sum('quantity'), 2),
                    'unit' => $ingredient->unit ?? 'kg',
                    'usage_count' => $items->count(),
                    'value' => round($items->sum(fn($i) => $i->quantity * 100), 2),
                ];
            })
            ->sortByDesc('total_consumed')
            ->take(10)
            ->values();

        // 🔹 Trend info
        $dailyUsage = $movements->groupBy(function ($move) {
            return Carbon::parse($move->created_at)->format('Y-m-d');
        })->map(fn($items) => $items->sum('quantity'));

        $peakDay = null;
        if ($dailyUsage->isNotEmpty()) {
            $maxDay = $dailyUsage->sortDesc()->keys()->first();
            $peakDay = [
                'date' => Carbon::parse($maxDay)->format('M d, Y'),
                'value' => round($dailyUsage[$maxDay], 2),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'report_type' => 'consumption',
                'summary' => [
                    'total_consumed' => $totalConsumed,
                    'total_quantity' => round($totalQuantity, 2),
                    'total_value' => round($totalValue, 2),
                    'avg_daily' => round($avgDaily, 2),
                    'trends' => [
                        'peak_day' => $peakDay['date'] ?? 'N/A',
                        'peak_value' => $peakDay['value'] ?? 0,
                        'avg_per_day' => round($avgDaily, 2),
                        'direction' => 'up',
                        'change_percentage' => 0,
                    ],
                ],
                'consumption_data' => $groupedConsumption,
                'top_consumed' => $topConsumed,
            ],
        ]);
    }


    private function getCategoryIcon($category)
    {
        $icons = [
            'meat' => 'drumstick-bite',
            'vegetables' => 'carrot',
            'soupbase' => 'bowl-hot',
            'beverage' => 'glass-water',
        ];
        return $icons[strtolower($category)] ?? 'box';
    }

    private function getCategoryColor($category)
    {
        $colors = [
            'meat' => 'red',
            'vegetables' => 'green',
            'soupbase' => 'orange',
            'beverage' => 'blue',
        ];
        return $colors[strtolower($category)] ?? 'gray';
    }


    public function getMenuReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Get all completed transaction IDs in the date range
        $completedTransactionIds = transaction::where('status', 'Completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('id');

        // Get served orders from reservations with completed transactions
        $reservationOrders = orders::with('menu')
            ->where('status', 'Served')
            ->whereHas('reservation', function ($query) use ($completedTransactionIds) {
                $query->whereHas('transactions', function ($q) use ($completedTransactionIds) {
                    $q->whereIn('id', $completedTransactionIds);
                });
            })
            ->get();

        // Get served orders from walk-ins with completed transactions
        $walkinOrders = orders::with('menu')
            ->where('status', 'Served')
            ->whereHas('walkin', function ($query) use ($completedTransactionIds) {
                $query->whereHas('transactions', function ($q) use ($completedTransactionIds) {
                    $q->whereIn('id', $completedTransactionIds);
                });
            })
            ->get();

        $servedOrders = $reservationOrders->merge($walkinOrders);

        $totalItemsSold = $servedOrders->sum('quantity');

        $totalRevenue = $servedOrders->sum(function ($order) {
            return $order->quantity * $order->price;
        });

        $menuPerformance = $servedOrders->groupBy('menu_id')->map(function ($orders) {
            $menu = $orders->first()->menu;
            if (!$menu) {
                return null;
            }

            $quantity = $orders->sum('quantity');
            $revenue = $orders->sum(function ($order) {
                return $order->quantity * $order->price;
            });

            return [
                'menu_id' => $menu->id,
                'menu_item' => $menu->menu_item,
                'quantity' => $quantity,
                'revenue' => $revenue,
            ];
        })->filter()->sortByDesc('quantity')->values();

        $bestSelling = $menuPerformance->first();

        $soldMenuIds = $menuPerformance->pluck('menu_id')->toArray();
        $zeroSalesItems = Menu::whereNotIn('id', $soldMenuIds)
            ->where('status', 'available')
            ->get()
            ->map(function ($menu) {
                return [
                    'menu_id' => $menu->id,
                    'menu_item' => $menu->menu_item,
                    'quantity' => 0,
                    'revenue' => 0,
                ];
            });

        $allMenuItems = $menuPerformance->concat($zeroSalesItems);

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_items_sold' => $totalItemsSold,
                    'total_revenue' => round($totalRevenue, 2),
                    'best_selling' => $bestSelling ? [
                        'name' => $bestSelling['menu_item'],
                        'quantity' => $bestSelling['quantity'],
                        'revenue' => round($bestSelling['revenue'], 2),
                    ] : null,
                ],
                'menu_items' => $allMenuItems,
            ]
        ]);
    }
    public function salesReportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $dateFrom = Carbon::parse($request->start_date)->startOfDay();
        $dateTo = Carbon::parse($request->end_date)->endOfDay();

        $transactions = transaction::with([
            'transactionDetails.orders.menu',
            'reservation',
            'walkin'
        ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'Completed')
            ->get();

        $grossSales = $transactions->sum('orders_total');
        $netSales = $transactions->sum('grand_total');
        $totalDiscounts = $transactions->sum('discount_total');
        $totalOrders = $transactions->count();

        $totalCustomers = $transactions->sum(function ($t) {
            return $t->reservation?->pax ?? $t->walkin?->pax ?? 0;
        });

        // E-wallet totals
        $gcashTotal = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$dateFrom, $dateTo])
            ->where('rpd.payment_method', 'gcash')
            ->sum('rpd.advance_payment');

        $mayaTotal = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$dateFrom, $dateTo])
            ->where('rpd.payment_method', 'maya')
            ->sum('rpd.advance_payment');

        $ewalletTotal = $gcashTotal + $mayaTotal;

        // Daily breakdown
        $dailyBreakdown = transaction::where('status', 'Completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(grand_total) as sales'),
                DB::raw('SUM(orders_total) as gross_sales'),
                DB::raw('SUM(discount_total) as discounts')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Get all transaction details
        $allDetails = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction->transactionDetails as $detail) {
                $order = $detail->orders;
                $price = $order ? ($order->price ?? 0) : 0;
                $quantity = $detail->quantity ?? 0;
                $discount = abs($detail->discount_amount ?? 0);

                $computedTotal = max(0, ($price * $quantity) - $discount);

                $allDetails[] = [
                    'item_name' => $detail->item_name,
                    'quantity' => $quantity,
                    'total' => $computedTotal,
                ];
            }
        }

        $groupedSales = collect($allDetails)
            ->groupBy('item_name')
            ->map(function ($items, $name) {
                return [
                    'item_name' => $name,
                    'quantity' => $items->sum('quantity'),
                    'total' => $items->sum('total'),
                ];
            })
            ->sortByDesc('total')
            ->values();

        try {
            $pdf = PDF::loadView('admin.reports.pdf-sales', [
                'groupedSales' => $groupedSales,
                'grossSales' => $grossSales,
                'netSales' => $netSales,
                'totalDiscounts' => $totalDiscounts,
                'totalOrders' => $totalOrders,
                'totalCustomers' => $totalCustomers,
                'ewalletTotal' => $ewalletTotal,
                'gcashTotal' => $gcashTotal,
                'mayaTotal' => $mayaTotal,
                'dailyBreakdown' => $dailyBreakdown,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'generatedAt' => now(),
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Sales_Report_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
        }
    }
    public function transactionReportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $dateFrom = Carbon::parse($request->start_date)->startOfDay();
        $dateTo = Carbon::parse($request->end_date)->endOfDay();

        $transactions = transaction::with(['reservation.customer', 'walkin', 'cashier'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'Completed')
            ->get();

        $totalTransactions = $transactions->count();
        $totalAmount = $transactions->sum('grand_total');

        // Cashier grouping with firstname + lastname
        $cashierData = $transactions
            ->groupBy('cashier_id')
            ->map(function ($group) {
                $cashier = optional($group->first()->cashier);
                return [
                    'cashier_name' => $cashier
                        ? "{$cashier->firstname} {$cashier->lastname}"
                        : 'Unknown Cashier',
                    'transaction_count' => $group->count(),
                    'total_amount' => $group->sum('grand_total'),
                ];
            })
            ->values()
            ->toArray();

        $data = [
            'summary' => [
                'total_transactions' => $totalTransactions,
                'total_amount' => round($totalAmount, 2),
                'date_range' => [
                    'from' => $dateFrom->format('M d, Y'),
                    'to' => $dateTo->format('M d, Y'),
                ],
            ],
            'cashier_summary' => $cashierData,
        ];

        try {
            $pdf = PDF::loadView('admin.reports.pdf-transaction', [
                'data' => $data,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'generatedAt' => now(),
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Transaction_Report_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
        }
    }



    private function getStockMovementData($startDate, $endDate)
    {
        $movements = ingredientMovements::with(['ingredient', 'user', 'ingredientBatch'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $stockIn = $movements->where('type', 'stock_in')->count();
        $stockOut = $movements->whereIn('type', ['stock_out', 'used'])->count();
        $expired = $movements->where('type', 'expired')->count();

        $stockInQty = $movements->where('type', 'stock_in')->sum('quantity');
        $stockOutQty = $movements->whereIn('type', ['stock_out', 'used'])->sum('quantity');
        $expiredQty = $movements->where('type', 'expired')->sum('quantity');

        return [
            'summary' => [
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'expired' => $expired,
                'total_movements' => $movements->count(),
                'stock_in_qty' => round($stockInQty, 2),
                'stock_out_qty' => round($stockOutQty, 2),
                'expired_qty' => round($expiredQty, 2),
            ],
            'movements' => $movements,
        ];
    }

    private function getExpiredItemsData($startDate, $endDate)
    {
        $expiredItems = expiredIngredients::with(['ingredientBatch.ingredient'])
            ->whereBetween('expired_at', [$startDate, $endDate])
            ->get();

        $expiringSoon = ingredientBatch::with('ingredient')
            ->where('status', 'active')
            ->whereRaw('DATEDIFF(expiration_date, CURDATE()) BETWEEN 0 AND 7')
            ->get();

        $totalWasteQty = $expiredItems->sum('quantity');

        $byCategory = $expiredItems->groupBy(function ($item) {
            return $item->ingredientBatch->ingredient->category ?? 'Unknown';
        })->map(function ($items, $category) {
            return [
                'name' => $category,
                'count' => $items->count(),
            ];
        });

        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();

        $thisWeekExpired = expiredIngredients::whereBetween('expired_at', [$thisWeekStart, Carbon::now()])->count();
        $thisMonthExpired = expiredIngredients::whereBetween('expired_at', [$thisMonthStart, Carbon::now()])->count();

        return [
            'summary' => [
                'expired_count' => $expiredItems->count(),
                'expiring_soon_count' => $expiringSoon->count(),
                'total_waste_qty' => round($totalWasteQty, 2),
                'by_category' => $byCategory,
                'trend' => [
                    'this_week' => $thisWeekExpired,
                    'this_month' => $thisMonthExpired,
                    'avg_per_week' => round($expiredItems->count() / max(1, $startDate->diffInWeeks($endDate) ?: 1), 1),
                ],
            ],
            'expired_items' => $expiredItems,
            'expiring_soon' => $expiringSoon,
        ];
    }

    private function getConsumptionData($startDate, $endDate)
    {
        $movements = ingredientMovements::with(['ingredient', 'order'])
            ->where('type', 'used')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalQuantity = $movements->sum('quantity');
        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);

        $byCategory = $movements->groupBy(function ($move) {
            return $move->ingredient->category ?? 'Unknown';
        })->map(function ($items, $category) use ($totalQuantity) {
            $qty = $items->sum('quantity');
            return [
                'name' => $category,
                'quantity' => round($qty, 2),
                'percentage' => $totalQuantity > 0 ? round(($qty / $totalQuantity) * 100, 1) : 0,
            ];
        });

        $topConsumed = $movements->groupBy('ingredient_id')
            ->map(function ($items) {
                $ingredient = $items->first()->ingredient;
                return [
                    'name' => $ingredient->name,
                    'category' => $ingredient->category,
                    'total_consumed' => round($items->sum('quantity'), 2),
                    'unit' => $ingredient->unit,
                    'usage_count' => $items->count(),
                ];
            })
            ->sortByDesc('total_consumed')
            ->take(10);

        return [
            'summary' => [
                'total_consumed' => $movements->count(),
                'total_quantity' => round($totalQuantity, 2),
                'avg_daily' => round($totalQuantity / $periodDays, 2),
                'by_category' => $byCategory,
            ],
            'consumption_data' => $movements,
            'top_consumed' => $topConsumed,
        ];
    }

    private function getInventoryPdfFilename($reportType)
    {
        $typeNames = [
            'stock-movement' => 'Stock_Movement_Report',
            'consumption' => 'Consumption_Report',
            'expired' => 'Expired_Items_Report',
        ];

        $typeName = $typeNames[$reportType] ?? 'Inventory_Report';
        $date = now()->format('Y-m-d_His');

        return $typeName . '_' . $date . '.pdf';
    }

    private function getInventoryReportData($reportType, $startDate, $endDate)
    {
        try {
            switch ($reportType) {
                case 'stock-movement':
                    $response = $this->getStockMovementReport($startDate, $endDate);
                    break;
                case 'expired':
                    $response = $this->getExpiredItemsReport($startDate, $endDate);
                    break;
                case 'consumption':
                    $response = $this->getConsumptionReport($startDate, $endDate);
                    break;
                default:
                    return null;
            }

            $content = $response->getContent();
            $data = json_decode($content, true);


            return $data['data'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function inventoryReportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'report_type' => 'required|in:stock-movement,expired,consumption',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $reportType = $request->report_type;

        try {
            $reportData = $this->getInventoryReportData($reportType, $startDate, $endDate);

            $viewName = 'admin.reports.partials.pdf-' . $reportType;

            $pdf = PDF::loadView($viewName, [
                'reportType' => $reportType,
                'reportData' => $reportData,
                'dateFrom' => $startDate,
                'dateTo' => $endDate,
                'generatedAt' => now(),
            ])->setPaper('a4', 'portrait');

            $filename = $this->getInventoryPdfFilename($reportType, $startDate, $endDate);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    public function menuReportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $completedTransactionIds = transaction::where('status', 'Completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->pluck('id');

        $reservationOrders = orders::with('menu')
            ->where('status', 'Served')
            ->whereHas('reservation', function ($query) use ($completedTransactionIds) {
                $query->whereHas('transactions', function ($q) use ($completedTransactionIds) {
                    $q->whereIn('id', $completedTransactionIds);
                });
            })
            ->get();

        $walkinOrders = orders::with('menu')
            ->where('status', 'Served')
            ->whereHas('walkin', function ($query) use ($completedTransactionIds) {
                $query->whereHas('transactions', function ($q) use ($completedTransactionIds) {
                    $q->whereIn('id', $completedTransactionIds);
                });
            })
            ->get();

        $servedOrders = $reservationOrders->merge($walkinOrders);

        $totalItemsSold = $servedOrders->sum('quantity');

        $totalRevenue = $servedOrders->sum(function ($order) {
            return $order->quantity * $order->price;
        });

        $menuPerformance = $servedOrders->groupBy('menu_id')->map(function ($orders) {
            $menu = $orders->first()->menu;
            if (!$menu) {
                return null;
            }

            $quantity = $orders->sum('quantity');
            $revenue = $orders->sum(function ($order) {
                return $order->quantity * $order->price;
            });

            return [
                'menu_id' => $menu->id,
                'menu_item' => $menu->menu_item,
                'quantity' => $quantity,
                'revenue' => $revenue,
            ];
        })->filter()->sortByDesc('quantity')->values();

        $bestSelling = $menuPerformance->first();

        $soldMenuIds = $menuPerformance->pluck('menu_id')->toArray();
        $zeroSalesItems = Menu::whereNotIn('id', $soldMenuIds)
            ->where('status', 'available')
            ->get()
            ->map(function ($menu) {
                return [
                    'menu_id' => $menu->id,
                    'menu_item' => $menu->menu_item,
                    'quantity' => 0,
                    'revenue' => 0,
                ];
            });

        $allMenuItems = $menuPerformance->concat($zeroSalesItems);

        try {
            $pdf = PDF::loadView('admin.reports.pdf-menu', [
                'totalItemsSold' => $totalItemsSold,
                'totalRevenue' => $totalRevenue,
                'bestSelling' => $bestSelling,
                'menuItems' => $allMenuItems,
                'dateFrom' => $startDate,
                'dateTo' => $endDate,
                'generatedAt' => now(),
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Menu_Performance_Report_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
        }
    }
}
