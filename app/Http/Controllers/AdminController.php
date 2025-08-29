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

        // Only today's transactions
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
            return Carbon::parse($t->created_at)->format('D'); // Mon, Tue, ...
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

    public function menu_management()
    {
        $menu = DB::table('menu')->get();
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

        ]);

        return redirect()->route('admin.menu_management')->with('success', 'Menu item added successfully!');
    }
    public function editMenu($id)
    {
        $menuItem = DB::table('menu')->where('id', $id)->first();
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
        ]);

        return redirect()->route('admin.menu_management')->with('success', 'Menu item updated successfully!');
    }

    public function table_management()
    {
        $tables = DB::table('tables')->get();
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

        ]);

        return redirect()->route('admin.table_management')->with('success', 'Table added successfully!');
    }

    public function editTable($id)
    {
        $table = DB::table('tables')->where('id', $id)->first();
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
        ]);

        return redirect()->route('admin.table_management')->with('success', 'Table updated successfully!');
    }

    public function stock_management()
    {
        $stocks = DB::table('stock')->get();
        return view('admin.stock_management', compact('stocks'));
    }

    public function storeStock(Request $request)
    {
        $request->validate([
            'stock_name' => 'required|string|max:255',
            'stock_quantity' => 'required|integer|min:1',

        ]);

        DB::table('stock')->insert([

            'stock_name' => $request->stock_name,
            'stock_quantity' => $request->stock_quantity,
        ]);


        return redirect()->route('admin.stock_management')->with('success', 'Stock item added successfully!');
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
            'updated_at' => now()
        ]);

        return redirect()->route('admin.stock_management')->with('success', 'Stock item updated successfully!');
    }

    public function editStock($id)
    {
        $stockItem = DB::table('stock')->where('id', $id)->first();
        if (!$stockItem) {
            return redirect()->route('admin.stock_management')->with('error', 'Stock item not found!');
        }
        return view('admin.editstock', compact('stockItem'));
    }

    public function reports()
    {

        return view('admin.reports');
    }

    public function feedback()
    {
        $feedbacks = DB::table('feedback')->get();
        return view('admin.feedback', compact('feedbacks'));
    }
}
