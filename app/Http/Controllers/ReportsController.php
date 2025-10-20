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

        // Get all transactions
        $transactions = transaction::with(['reservation.customer', 'walkin'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Summary metrics
        $totalTransactions = $transactions->count();
        $completedTransactions = $transactions->where('status', 'Completed')->count();
        $pendingTransactions = $transactions->where('status', 'Pending')->count();
        $totalAmount = $transactions->where('status', 'Completed')->sum('grand_total');

        // Payment method breakdown
        $cashTransactions = $transactions->where('status', 'Completed')
            ->where('payment_method', 'cash')->count();
        $cashAmount = $transactions->where('status', 'Completed')
            ->where('payment_method', 'cash')->sum('grand_total');

        // E-wallet from reservation payments
        $ewalletTransactions = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->whereIn('rpd.payment_method', ['gcash', 'maya'])
            ->count();

        $ewalletAmount = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$startDate, $endDate])
            ->whereIn('rpd.payment_method', ['gcash', 'maya'])
            ->sum('rpd.advance_payment');

        // Transaction list
        $transactionList = $transactions->map(function ($transaction) {
            $customerName = 'Walk-in Customer';
            $orderType = 'Walk-in';

            if ($transaction->reservation) {
                $customerName = $transaction->reservation->customer->name ?? 'N/A';
                $orderType = 'Reservation';
            } elseif ($transaction->walkin) {
                $customerName = 'Walk-in #' . $transaction->walkin->id;
            }

            return [
                'id' => $transaction->id,
                'date' => Carbon::parse($transaction->created_at)->format('M d, Y h:i A'),
                'customer_name' => $customerName,
                'order_type' => $orderType,
                'amount' => floatval($transaction->grand_total),
                'payment_method' => ucfirst($transaction->payment_method ?? 'N/A'),
                'status' => $transaction->status,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_transactions' => $totalTransactions,
                    'completed_transactions' => $completedTransactions,
                    'pending_transactions' => $pendingTransactions,
                    'total_amount' => round($totalAmount, 2),
                    'cash_transactions' => $cashTransactions,
                    'cash_amount' => round($cashAmount, 2),
                    'ewallet_transactions' => $ewalletTransactions,
                    'ewallet_amount' => round($ewalletAmount, 2),
                ],
                'transactions' => $transactionList,
            ]
        ]);
    }

    public function getInventoryReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:current-stock,stock-movement,low-stock,batch-tracking,expired,consumption',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $reportType = $request->report_type;

        switch ($reportType) {
            case 'current-stock':
                return $this->getCurrentStockReport();
            case 'stock-movement':
                return $this->getStockMovementReport($startDate, $endDate);
            case 'low-stock':
                return $this->getLowStockReport();
            case 'batch-tracking':
                return $this->getBatchTrackingReport();
            case 'expired':
                return $this->getExpiredItemsReport($startDate, $endDate);
            case 'consumption':
                return $this->getConsumptionReport($startDate, $endDate);
            default:
                return response()->json(['success' => false, 'message' => 'Invalid report type'], 400);
        }
    }

    private function getCurrentStockReport()
    {
        $ingredients = ingredients::with('stockAlertLevel')->get();

        $totalItems = $ingredients->count();
        $inStock = $ingredients->filter(fn($i) => $i->stocks > ($i->stockAlertLevel->low_stock ?? 0))->count();
        $lowStock = $ingredients->filter(
            fn($i) =>
            $i->stockAlertLevel &&
                $i->stocks <= $i->stockAlertLevel->low_stock &&
                $i->stocks > $i->stockAlertLevel->critical_stock
        )->count();
        $outOfStock = $ingredients->filter(
            fn($i) =>
            $i->stockAlertLevel &&
                $i->stocks <= $i->stockAlertLevel->critical_stock
        )->count();

        $categories = $ingredients->groupBy('category')->map(function ($items, $category) {
            return [
                'name' => $category,
                'count' => $items->count(),
                'icon' => $this->getCategoryIcon($category),
                'color' => $this->getCategoryColor($category),
            ];
        })->values();

        $healthyPercentage = $totalItems > 0 ? round(($inStock / $totalItems) * 100, 1) : 0;
        $attentionPercentage = $totalItems > 0 ? round((($lowStock + $outOfStock) / $totalItems) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_items' => $totalItems,
                    'in_stock' => $inStock,
                    'low_stock' => $lowStock,
                    'out_of_stock' => $outOfStock,
                    'healthy_percentage' => $healthyPercentage,
                    'attention_percentage' => $attentionPercentage,
                    'categories' => $categories,
                ],
                'ingredients' => $ingredients->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'category' => $item->category,
                        'stocks' => round($item->stocks, 2),
                        'unit' => $item->unit,
                        'status' => $this->getStockStatusText($item),
                    ];
                }),
            ]
        ]);
    }

    private function getStockMovementReport($startDate, $endDate)
    {
        $movements = ingredientMovements::with(['ingredient', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $stockIn = $movements->where('type', 'stock_in')->count();
        $stockOut = $movements->whereIn('type', ['stock_out', 'used'])->count();
        $adjustments = $movements->where('type', 'adjustment')->count();

        $stockInQty = $movements->where('type', 'stock_in')->sum('quantity');
        $stockOutQty = $movements->whereIn('type', ['stock_out', 'used'])->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'stock_in' => $stockIn,
                    'stock_out' => $stockOut,
                    'adjustments' => $adjustments,
                    'total_movements' => $movements->count(),
                    'stock_in_qty' => round($stockInQty, 2),
                    'stock_out_qty' => round($stockOutQty, 2),
                    'unit' => 'kg/L',
                ],
                'movements' => $movements->map(function ($move) {
                    return [
                        'date' => $move->created_at->format('M d, Y h:i A'),
                        'ingredient' => $move->ingredient->name ?? 'Unknown',
                        'type' => $move->type,
                        'quantity' => round($move->quantity, 2),
                        'stock_before' => round($move->stock_before, 2),
                        'stock_after' => round($move->stock_after, 2),
                        'notes' => $move->notes,
                    ];
                }),
            ]
        ]);
    }

    private function getLowStockReport()
    {
        $ingredients = ingredients::with('stockAlertLevel')
            ->whereHas('stockAlertLevel')
            ->get();

        $lowStockItems = $ingredients->filter(function ($item) {
            return $item->stockAlertLevel &&
                $item->stocks <= $item->stockAlertLevel->low_stock;
        });

        $criticalItems = $lowStockItems->filter(function ($item) {
            return $item->stocks <= $item->stockAlertLevel->critical_stock;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'low_stock_count' => $lowStockItems->count(),
                    'critical_count' => $criticalItems->count(),
                    'reorder_needed' => $lowStockItems->filter(
                        fn($i) =>
                        $i->stockAlertLevel->reorder_quantity > 0
                    )->count(),
                ],
                'low_stock_items' => $lowStockItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'category' => $item->category,
                        'stocks' => round($item->stocks, 2),
                        'unit' => $item->unit,
                        'low_stock' => round($item->stockAlertLevel->low_stock, 2),
                        'reorder_quantity' => round($item->stockAlertLevel->reorder_quantity, 2),
                        'priority' => $item->stocks <= $item->stockAlertLevel->critical_stock ? 'Critical' : ($item->stocks <= ($item->stockAlertLevel->low_stock * 0.5) ? 'High' : 'Medium'),
                    ];
                })->sortBy(function ($item) {
                    $priority = ['Critical' => 1, 'High' => 2, 'Medium' => 3];
                    return $priority[$item['priority']];
                })->values(),
                'critical_items' => $criticalItems->map(function ($item) {
                    return [
                        'name' => $item->name,
                        'stocks' => round($item->stocks, 2),
                        'unit' => $item->unit,
                    ];
                })->values(),
            ]
        ]);
    }

    private function getBatchTrackingReport()
    {
        $batches = ingredientBatch::with('ingredient')
            ->whereIn('status', ['active', 'expired'])
            ->orderBy('expiration_date', 'asc')
            ->get();

        $today = Carbon::today();

        $activeBatches = $batches->where('status', 'active')->count();
        $expiredBatches = $batches->where('status', 'expired')->count();
        $expiringSoon = $batches->filter(function ($batch) use ($today) {
            $expirationDate = Carbon::parse($batch->expiration_date);
            $daysLeft = $today->diffInDays($expirationDate, false);
            return $daysLeft >= 0 && $daysLeft <= 7 && $batch->status === 'active';
        })->count();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_batches' => $batches->count(),
                    'active_batches' => $activeBatches,
                    'expiring_soon' => $expiringSoon,
                    'expired_batches' => $expiredBatches,
                ],
                'batches' => $batches->map(function ($batch) use ($today) {
                    $expirationDate = Carbon::parse($batch->expiration_date);
                    $daysLeft = $today->diffInDays($expirationDate, false);

                    return [
                        'id' => $batch->id,
                        'ingredient' => $batch->ingredient->name ?? 'Unknown',
                        'quantity' => round($batch->quantity, 2),
                        'unit' => $batch->ingredient->unit ?? 'kg',
                        'arrived_at' => Carbon::parse($batch->arrived_at)->format('M d, Y'),
                        'expiration_date' => $expirationDate->format('M d, Y'),
                        'days_left' => (int) $daysLeft,
                        'status' => $batch->status,
                    ];
                }),
            ]
        ]);
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
            // Assuming average cost per kg - adjust based on your pricing logic
            return $item->quantity * 100; // placeholder value
        });

        $byCategory = $expiredItems->groupBy(function ($item) {
            return $item->ingredientBatch->ingredient->category ?? 'Unknown';
        })->map(function ($items, $category) {
            return [
                'name' => $category,
                'count' => $items->count(),
                'value' => $items->sum(fn($i) => $i->quantity * 100),
                'icon' => $this->getCategoryIcon($category),
                'color' => $this->getCategoryColor($category),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'expired_count' => $expiredItems->count(),
                    'expired_value' => round($totalWasteValue, 2),
                    'expiring_soon_count' => $expiringSoonBatches->count(),
                    'total_waste_qty' => round($totalWasteQty, 2),
                    'total_waste_value' => round($totalWasteValue, 2),
                    'by_category' => $byCategory,
                    'trend' => [
                        'this_week' => $expiredItems->where('expired_at', '>=', Carbon::now()->startOfWeek())->count(),
                        'this_week_value' => round($expiredItems->where('expired_at', '>=', Carbon::now()->startOfWeek())->sum(fn($i) => $i->quantity * 100), 2),
                        'this_month' => $expiredItems->where('expired_at', '>=', Carbon::now()->startOfMonth())->count(),
                        'this_month_value' => round($expiredItems->where('expired_at', '>=', Carbon::now()->startOfMonth())->sum(fn($i) => $i->quantity * 100), 2),
                        'avg_per_week' => round($expiredItems->count() / max(1, $startDate->diffInWeeks($endDate)), 1),
                    ],
                ],
                'expired_items' => $expiredItems->map(function ($item) {
                    $expiredDate = Carbon::parse($item->expired_at);
                    return [
                        'name' => $item->ingredientBatch->ingredient->name ?? 'Unknown',
                        'category' => $item->ingredientBatch->ingredient->category ?? 'Unknown',
                        'batch_id' => $item->ingredient_batch_id,
                        'quantity' => round($item->quantity, 2),
                        'unit' => $item->ingredientBatch->ingredient->unit ?? 'kg',
                        'expiration_date' => $expiredDate->format('M d, Y'),
                        'days_expired' => Carbon::now()->diffInDays($expiredDate),
                        'value_lost' => round($item->quantity * 100, 2),
                    ];
                }),
                'expiring_soon' => $expiringSoonBatches->map(function ($batch) {
                    $expirationDate = Carbon::parse($batch->expiration_date);
                    return [
                        'name' => $batch->ingredient->name,
                        'expiration_date' => $expirationDate->format('M d, Y'),
                        'days_until_expiry' => Carbon::today()->diffInDays($expirationDate),
                    ];
                }),
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
        $totalValue = $movements->sum(fn($m) => $m->quantity * 100); // placeholder cost calculation

        $periodDays = max(1, $startDate->diffInDays($endDate) + 1);
        $avgDaily = $totalQuantity / $periodDays;

        $byCategory = $movements->groupBy(function ($move) {
            return $move->ingredient->category ?? 'Unknown';
        })->map(function ($items, $category) use ($totalQuantity) {
            $qty = $items->sum('quantity');
            $percentage = $totalQuantity > 0 ? round(($qty / $totalQuantity) * 100, 1) : 0;

            return [
                'name' => $category,
                'quantity' => round($qty, 2),
                'value' => round($items->sum(fn($i) => $i->quantity * 100), 2),
                'percentage' => $percentage,
                'icon' => $this->getCategoryIcon($category),
                'color' => $this->getCategoryColor($category),
            ];
        })->values();

        $topConsumed = $movements->groupBy('ingredient_id')
            ->map(function ($items) {
                $ingredient = $items->first()->ingredient;
                return [
                    'name' => $ingredient->name,
                    'total_consumed' => round($items->sum('quantity'), 2),
                    'unit' => $ingredient->unit,
                    'usage_count' => $items->count(),
                    'value' => round($items->sum(fn($i) => $i->quantity * 100), 2),
                ];
            })
            ->sortByDesc('total_consumed')
            ->take(10)
            ->values();

        $dailyUsage = $movements->groupBy(function ($move) {
            return Carbon::parse($move->created_at)->format('Y-m-d');
        })->map(fn($items) => $items->sum('quantity'));

        $peakDay = $dailyUsage->keys()->zip($dailyUsage->values())
            ->sortByDesc(fn($item) => $item[1])
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_consumed' => $totalConsumed,
                    'total_quantity' => round($totalQuantity, 2),
                    'total_value' => round($totalValue, 2),
                    'avg_daily' => round($avgDaily, 2),
                    'by_category' => $byCategory,
                    'trends' => [
                        'peak_day' => $peakDay ? Carbon::parse($peakDay[0])->format('M d, Y') : 'N/A',
                        'peak_value' => $peakDay ? round($peakDay[1], 2) : 0,
                        'avg_per_day' => round($avgDaily, 2),
                        'direction' => 'up', // Calculate based on comparison logic
                        'change_percentage' => 0, // Calculate vs previous period
                    ],
                ],
                'consumption_data' => $movements->map(function ($move) {
                    return [
                        'date' => $move->created_at->format('M d, Y'),
                        'ingredient' => $move->ingredient->name ?? 'Unknown',
                        'category' => $move->ingredient->category ?? 'Unknown',
                        'quantity' => round($move->quantity, 2),
                        'unit' => $move->ingredient->unit ?? 'kg',
                        'used_for' => $move->order_id ? "Order #{$move->order_id}" : null,
                        'value' => round($move->quantity * 100, 2),
                    ];
                }),
                'top_consumed' => $topConsumed,
            ]
        ]);
    }

    private function getCategoryIcon($category)
    {
        $icons = [
            'vegetable' => 'carrot',
            'meat' => 'drumstick-bite',
            'spice' => 'pepper-hot',
            'dairy' => 'cheese',
            'seafood' => 'fish',
        ];
        return $icons[strtolower($category)] ?? 'box';
    }

    private function getCategoryColor($category)
    {
        $colors = [
            'vegetable' => 'green',
            'meat' => 'red',
            'spice' => 'orange',
            'dairy' => 'yellow',
            'seafood' => 'blue',
        ];
        return $colors[strtolower($category)] ?? 'gray';
    }

    private function getStockStatusText($ingredient)
    {
        if (!$ingredient->stockAlertLevel) {
            return 'In Stock';
        }

        if ($ingredient->stocks <= $ingredient->stockAlertLevel->critical_stock) {
            return 'Out of Stock';
        }

        if ($ingredient->stocks <= $ingredient->stockAlertLevel->low_stock) {
            return 'Low Stock';
        }

        return 'In Stock';
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

        // Merge both order collections
        $servedOrders = $reservationOrders->merge($walkinOrders);

        // Calculate total items sold
        $totalItemsSold = $servedOrders->sum('quantity');

        // Calculate total revenue
        $totalRevenue = $servedOrders->sum(function ($order) {
            return $order->quantity * $order->price;
        });

        // Group by menu items
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

        // Get best-selling item
        $bestSelling = $menuPerformance->first();

        // Get all menu items with zero sales
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

        // Merge sold items with zero sales items
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

        // Get all transactions
        $transactions = transaction::with(['reservation.customer', 'walkin'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get();

        // Summary metrics
        $totalTransactions = $transactions->count();
        $completedTransactions = $transactions->where('status', 'Completed')->count();
        $pendingTransactions = $transactions->where('status', 'Pending')->count();
        $totalAmount = $transactions->where('status', 'Completed')->sum('grand_total');

        // Payment method breakdown
        $cashTransactions = $transactions->where('status', 'Completed')
            ->where('payment_method', 'cash')->count();
        $cashAmount = $transactions->where('status', 'Completed')
            ->where('payment_method', 'cash')->sum('grand_total');

        // E-wallet from reservation payments
        $ewalletTransactions = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$dateFrom, $dateTo])
            ->whereIn('rpd.payment_method', ['gcash', 'maya'])
            ->count();

        $ewalletAmount = DB::table('reservation_payment_details as rpd')
            ->join('transactions as t', 'rpd.reservation_id', '=', 't.reservation_id')
            ->where('t.status', 'Completed')
            ->whereBetween('t.created_at', [$dateFrom, $dateTo])
            ->whereIn('rpd.payment_method', ['gcash', 'maya'])
            ->sum('rpd.advance_payment');

        // Transaction list
        $transactionList = $transactions->map(function ($transaction) {
            $customerName = 'Walk-in Customer';
            $orderType = 'Walk-in';

            if ($transaction->reservation) {
                $customerName = $transaction->reservation->customer->name ?? 'N/A';
                $orderType = 'Reservation';
            } elseif ($transaction->walkin) {
                $customerName = 'Walk-in #' . $transaction->walkin->id;
            }

            return [
                'id' => $transaction->id,
                'date' => Carbon::parse($transaction->created_at)->format('M d, Y h:i A'),
                'customer_name' => $customerName,
                'order_type' => $orderType,
                'amount' => floatval($transaction->grand_total),
                'payment_method' => ucfirst($transaction->payment_method ?? 'N/A'),
                'status' => $transaction->status,
            ];
        });

        try {
            $pdf = PDF::loadView('admin.reports.pdf-transaction', [
                'totalTransactions' => $totalTransactions,
                'completedTransactions' => $completedTransactions,
                'pendingTransactions' => $pendingTransactions,
                'totalAmount' => $totalAmount,
                'cashTransactions' => $cashTransactions,
                'cashAmount' => $cashAmount,
                'ewalletTransactions' => $ewalletTransactions,
                'ewalletAmount' => $ewalletAmount,
                'transactionList' => $transactionList,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'generatedAt' => now(),
            ])->setPaper('a4', 'portrait');

            return $pdf->download('Transaction_Report_' . now()->format('Ymd_His') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
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
