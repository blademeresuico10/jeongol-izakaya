<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\feedback;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\customers;
use App\Models\Stock;


class AdminController extends Controller
{
    public function index()
    {
        $todayRevenue = transaction::whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        $todayCustomers = customers::whereDate('created_at', Carbon::today())
            ->count();

        $stocks = Stock::all();

        $totalStock = $stocks->sum('stock_quantity');

        $stockChartData = $stocks->map(function ($stock) use ($totalStock) {
            return [
                'name' => $stock->stock_name,
                'quantity' => $stock->stock_quantity,
                'percentage' => $totalStock > 0 ? ($stock->stock_quantity / $totalStock) * 100 : 0
            ];
        });

        $transactions = Transaction::with('cashier')
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->get();

        $weeklyRevenue = Transaction::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('D'))
            ->map(fn($day) => $day->sum('total_amount'));

        $monthlyRevenue = Transaction::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('M'))
            ->map(fn($month) => $month->sum('total_amount'));

        $quarterlyRevenue = Transaction::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(fn($t) => 'Q' . ceil(Carbon::parse($t->created_at)->month / 3))
            ->map(fn($q) => $q->sum('total_amount'));

        return view('admin.home', compact(
            'todayRevenue',
            'todayCustomers',
            'totalStock',
            'stockChartData',
            'transactions',
            'weeklyRevenue',
            'monthlyRevenue',
            'quarterlyRevenue'
        ));
    }

    public function dashboardData()
    {
        $todayRevenue = transaction::whereDate('created_at', Carbon::today())->sum('total_amount');
        $todayCustomers = customers::whereDate('created_at', Carbon::today())->count();
        $stocks = Stock::all();

        $totalStock = $stocks->sum('stock_quantity');
        $stockChartData = $stocks->map(fn($s) => [
            'name' => $s->stock_name,
            'quantity' => $s->stock_quantity,
            'percentage' => $totalStock > 0 ? ($s->stock_quantity / $totalStock) * 100 : 0
        ]);

        return response()->json([
            'revenue' => $todayRevenue,
            'customers' => $todayCustomers,
            'stock' => $stockChartData
        ]);
    }
    public function salesData()
    {
        $weeklyRevenue = Transaction::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->get()->groupBy(function ($t) {
            return Carbon::parse($t->created_at)->format('D');
        })->map(fn($day) => $day->sum('total_amount'));

        $monthlyRevenue = Transaction::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('M'))
            ->map(fn($month) => $month->sum('total_amount'));

        $quarterlyRevenue = Transaction::whereYear('created_at', Carbon::now()->year)
            ->get()
            ->groupBy(fn($t) => 'Q' . ceil(Carbon::parse($t->created_at)->month / 3))
            ->map(fn($q) => $q->sum('total_amount'));

        return response()->json([
            'weekly' => $weeklyRevenue,
            'monthly' => $monthlyRevenue,
            'quarterly' => $quarterlyRevenue,
        ]);
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function adduser()
    {
        return view('admin.adduser');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'role' => 'required|string',
            'contact' => 'required|string|max:20',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'role' => $request->role,
            'contact_number' => $request->contact,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users')->with('success', 'User added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
        ]);

        $user = User::findOrFail($id);

        $nameParts = explode(' ', $request->name, 2);
        $user->firstname = $nameParts[0];
        $user->lastname = $nameParts[1] ?? '';
        $user->role = $request->role;
        $user->status = $request->has('status') ? 1 : 0;


        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function menu_management(Request $request)
    {
        $showDeleted = $request->has('show_deleted');

        if ($showDeleted) {
            $menu = DB::table('menu')
                ->whereNotNull('deleted_at')
                ->get();
        } else {
            $menu = DB::table('menu')
                ->whereNull('deleted_at')
                ->get();
        }

        return view('admin.menu_management', compact('menu'));
    }

    public function addmenu()
    {
        return view('admin.addmenu');
    }

    public function storeMenu(Request $request)
    {
        $request->validate([
            'menu_item' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        DB::table('menu')->insert([
            'menu_item' => $request->menu_item,
            'price' => $request->price,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.menu_management')->with('success', 'Menu item added successfully!');
    }

    public function editMenu($id)
    {
        $menuItem = DB::table('menu')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$menuItem) {
            return redirect()->route('admin.menu_management')->with('error', 'Menu item not found!');
        }
        return view('admin.editmenu', compact('menuItem'));
    }

    public function updateMenu(Request $request, $id)
    {
        $request->validate([
            'menu_item' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        DB::table('menu')->where('id', $id)->update([
            'menu_item' => $request->menu_item,
            'price' => $request->price,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.menu_management')->with('success', 'Menu item updated successfully!');
    }

    public function deleteMenu($id)
    {
        $menuItem = DB::table('menu')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$menuItem) {
            return redirect()->back()->with('error', 'Menu item not found!');
        }

        DB::table('menu')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Menu item deleted successfully!');
    }


    public function restoreMenu($id)
    {
        $menuItem = DB::table('menu')->where('id', $id)->whereNotNull('deleted_at')->first();

        if (!$menuItem) {
            return redirect()->back()->with('error', 'Menu item not found or not deleted!');
        }

        DB::table('menu')->where('id', $id)->update([
            'deleted_at' => null,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Menu item restored successfully!');
    }


    public function forceDeleteMenu($id)
    {
        $menuItem = DB::table('menu')->where('id', $id)->first();

        if (!$menuItem) {
            return redirect()->back()->with('error', 'Menu item not found!');
        }

        DB::table('menu')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Menu item permanently deleted!');
    }


    public function table_management(Request $request)
    {
        $showDeleted = $request->has('show_deleted');

        if ($showDeleted) {
            // Show only soft-deleted tables
            $tables = DB::table('tables')
                ->whereNotNull('deleted_at')
                ->get();
        } else {
            // Show only active tables
            $tables = DB::table('tables')
                ->whereNull('deleted_at')
                ->get();
        }

        return view('admin.table_management', compact('tables'));
    }

    public function addtable()
    {
        return view('admin.addtable');
    }

    public function storeTable(Request $request)
    {
        $request->validate([
            'table_number' => 'required|integer|unique:tables,table_number',
            'capacity' => 'required|integer|min:1',
        ]);

        DB::table('tables')->insert([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.table_management')->with('success', 'Table added successfully!');
    }

    public function editTable($id)
    {
        $table = DB::table('tables')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$table) {
            return redirect()->route('admin.table_management')->with('error', 'Table not found!');
        }

        return view('admin.edittable', compact('table'));
    }

    public function updateTable(Request $request, $id)
    {
        $request->validate([
            'table_number' => 'required|integer',
            'capacity' => 'required|integer|min:1',
        ]);

        DB::table('tables')->where('id', $id)->update([
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.table_management')->with('success', 'Table updated successfully!');
    }

    public function deleteTable($id)
    {
        $table = DB::table('tables')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$table) {
            return redirect()->back()->with('error', 'Table not found!');
        }

        DB::table('tables')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Table deleted successfully!');
    }

    public function restoreTable($id)
    {
        $table = DB::table('tables')->where('id', $id)->whereNotNull('deleted_at')->first();

        if (!$table) {
            return redirect()->back()->with('error', 'Table not found or not deleted!');
        }

        DB::table('tables')->where('id', $id)->update([
            'deleted_at' => null,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Table restored successfully!');
    }

    public function forceDeleteTable($id)
    {
        $table = DB::table('tables')->where('id', $id)->first();

        if (!$table) {
            return redirect()->back()->with('error', 'Table not found!');
        }

        DB::table('tables')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Table permanently deleted!');
    }

    public function stock_management(Request $request)
    {
        $showDeleted = $request->has('show_deleted');

        if ($showDeleted) {
            // Show only soft-deleted stocks
            $stocks = DB::table('stock')
                ->whereNotNull('deleted_at')
                ->get();
        } else {
            // Show active stocks
            $stocks = DB::table('stock')
                ->whereNull('deleted_at')
                ->get();
        }

        return view('admin.stock_management', compact('stocks'));
    }

    public function storeStock(Request $request)
    {
        $request->validate([
            'stock_name' => 'required|string|max:255',
            'stock_quantity' => 'required|numeric|min:0',
        ]);

        DB::table('stock')->insert([
            'stock_name' => $request->stock_name,
            'stock_quantity' => $request->stock_quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.stock_management')->with('success', 'Stock item added successfully!');
    }

    public function editStock($id)
    {
        $stockItem = DB::table('stock')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$stockItem) {
            return redirect()->route('admin.stock_management')->with('error', 'Stock item not found!');
        }

        return view('admin.editstock', compact('stockItem'));
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'stock_name' => 'required|string|max:255',
            'stock_quantity' => 'required|numeric|min:0',
        ]);

        DB::table('stock')->where('id', $id)->update([
            'stock_name' => $request->stock_name,
            'stock_quantity' => $request->stock_quantity,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.stock_management')->with('success', 'Stock item updated successfully!');
    }

    public function deleteStock($id)
    {
        $stockItem = DB::table('stock')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$stockItem) {
            return redirect()->back()->with('error', 'Stock item not found!');
        }

        DB::table('stock')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Stock item deleted successfully!');
    }

    public function restoreStock($id)
    {
        $stockItem = DB::table('stock')->where('id', $id)->whereNotNull('deleted_at')->first();

        if (!$stockItem) {
            return redirect()->back()->with('error', 'Stock item not found or not deleted!');
        }

        DB::table('stock')->where('id', $id)->update([
            'deleted_at' => null,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Stock item restored successfully!');
    }

    public function forceDeleteStock($id)
    {
        $stockItem = DB::table('stock')->where('id', $id)->first();

        if (!$stockItem) {
            return redirect()->back()->with('error', 'Stock item not found!');
        }

        DB::table('stock')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Stock item permanently deleted!');
    }


    public function reports(Request $request)
    {
        $fromInput = $request->input('date_from') ?? $request->input('from_date');
        $toInput   = $request->input('date_to')   ?? $request->input('to_date');

        $dateFrom = $fromInput ? Carbon::parse($fromInput)->startOfDay() : Carbon::now()->startOfDay();
        $dateTo   = $toInput   ? Carbon::parse($toInput)->endOfDay()     : Carbon::now()->endOfDay();

        $totalSales = DB::table('transactions')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount') ?? 0;

        $totalPax = DB::table('reservations')
            ->where('status', 'Accepted')
            ->whereBetween('reservation_time', [$dateFrom, $dateTo])
            ->sum('pax') ?? 0;

        $totalDiscounts = DB::table('transactions')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('discount_amount') ?? 0;

        $productConsumption = DB::table('order_details')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->whereBetween('order_details.created_at', [$dateFrom, $dateTo])
            ->select(
                'menu.menu_item',
                'menu.category',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(COALESCE(order_details.order_price, menu.price * order_details.quantity)) as total_revenue')
            )
            ->groupBy('menu.id', 'menu.menu_item', 'menu.category')
            ->orderByDesc('total_quantity')
            ->get();


        $sales = DB::table('transactions')
            ->join('reservations', 'transactions.reservation_id', '=', 'reservations.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                'transactions.id as id',
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('COALESCE(tables.table_number, "N/A") as table_number'),
                DB::raw('COALESCE(customers.name, "Walk-in") as customer_name'),
                'reservations.pax',
                'transactions.subtotal',
                'transactions.discount_amount',
                'transactions.total_amount'
            )
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        $salesTrend = DB::table('transactions')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports', compact(
            'dateFrom',
            'dateTo',
            'totalSales',
            'totalPax',
            'totalDiscounts',
            'productConsumption',
            'sales',
            'salesTrend'
        ));
    }

    public function export(Request $request)
    {
        $fromInput = $request->input('from_date');
        $toInput   = $request->input('to_date');

        $dateFrom = $fromInput ? Carbon::parse($fromInput)->startOfDay() : Carbon::now()->startOfDay();
        $dateTo   = $toInput   ? Carbon::parse($toInput)->endOfDay()     : Carbon::now()->endOfDay();

        $totalSales = DB::table('transactions')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_amount') ?? 0;

        $totalPax = DB::table('reservations')
            ->where('status', 'Accepted')
            ->whereBetween('reservation_time', [$dateFrom, $dateTo])
            ->sum('pax') ?? 0;

        $totalDiscounts = DB::table('transactions')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('discount_amount') ?? 0;

        $productConsumption = DB::table('order_details')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->whereBetween('order_details.created_at', [$dateFrom, $dateTo])
            ->select(
                'menu.menu_item',
                'menu.category',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(COALESCE(order_details.order_price, menu.price * order_details.quantity)) as total_revenue')
            )
            ->groupBy('menu.id', 'menu.menu_item', 'menu.category')
            ->orderByDesc('total_quantity')
            ->get();

        $sales = DB::table('transactions')
            ->join('reservations', 'transactions.reservation_id', '=', 'reservations.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('COALESCE(tables.table_number, "N/A") as table_number'),
                DB::raw('COALESCE(customers.name, "Walk-in") as customer_name'),
                'reservations.pax',
                'transactions.subtotal',
                'transactions.discount_amount',
                'transactions.total_amount'
            )
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        $filename = 'sales-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($dateFrom, $dateTo, $totalSales, $totalPax, $totalDiscounts, $productConsumption, $sales) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['JEONGOL IZAKAYA - SALES REPORT']);
            fputcsv($file, ['Period: ' . $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y')]);
            fputcsv($file, ['Generated: ' . now()->format('M d, Y h:i A')]);
            fputcsv($file, []); 

            fputcsv($file, ['SUMMARY']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Sales', number_format($totalSales, 2)]);
            fputcsv($file, ['Total Pax', $totalPax]);
            fputcsv($file, ['Total Discounts', number_format($totalDiscounts, 2)]);
            fputcsv($file, []); 

            fputcsv($file, ['PRODUCT CONSUMPTION']);
            fputcsv($file, ['Product', 'Category', 'Quantity', 'Revenue']);
            foreach ($productConsumption as $product) {
                fputcsv($file, [
                    $product->menu_item,
                    ucfirst($product->category),
                    $product->total_quantity,
                    number_format($product->total_revenue, 2)
                ]);
            }
            fputcsv($file, []); 

            fputcsv($file, ['SALES BREAKDOWN']);
            fputcsv($file, ['Date', 'Table', 'Customer', 'Pax', 'Subtotal', 'Discount', 'Total']);
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->date,
                    $sale->table_number,
                    $sale->customer_name,
                    $sale->pax,
                    number_format($sale->subtotal, 2),
                    number_format($sale->discount_amount, 2),
                    number_format($sale->total_amount, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function feedback()
    {
        $feedbacks = DB::table('feedback')->get();
        return view('admin.feedback', compact('feedbacks'));
    }
}
