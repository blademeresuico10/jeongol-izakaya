<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\feedback;
use App\Models\transaction;
use Carbon\Carbon;
use App\Models\customers;
use App\Models\stock;
use App\Models\reservation;
use Barryvdh\DomPDF\Facade\pdf;


class AdminController extends Controller
{
    public function index()
    {
        $todayRevenue = DB::table('transactions')
            ->whereDate('created_at', Carbon::today())
            ->where('status', '!=', 'Refunded')
            ->sum('total');

        $todayCustomers = DB::table('reservations')
            ->whereDate('reservation_time', Carbon::today())
            ->whereIn('status', ['Accepted', 'Completed'])
            ->sum('pax');

        $stocks = DB::table('stock')->whereNull('deleted_at')->get();
        $totalStock = $stocks->sum('stock_quantity');

        $stockChartData = $stocks->map(function ($stock) use ($totalStock) {
            return [
                'name'       => $stock->stock_name,
                'quantity'   => $stock->stock_quantity,
                'percentage' => $totalStock > 0 ? ($stock->stock_quantity / $totalStock) * 100 : 0,
            ];
        });

        $transactions = DB::table('transactions')
            ->join('users', 'transactions.cashier_id', '=', 'users.id')
            ->select(
                'transactions.*',
                'users.firstname',
                'users.lastname'
            )
            ->whereDate('transactions.created_at', Carbon::today())
            ->where('transactions.status', '!=', 'Refunded')
            ->orderBy('transactions.created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                return (object)[
                    'id' => $transaction->id,
                    'total' => $transaction->total,
                    'advance_payment' => $transaction->advance_payment ?? 0,
                    'cashier' => (object)[
                        'firstname' => $transaction->firstname,
                        'lastname' => $transaction->lastname,
                    ]
                ];
            });

        // Weekly revenue
        $weeklyRevenue = DB::table('transactions')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->where('status', '!=', 'Refunded')
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('D'))
            ->map(fn($day) => $day->sum('total'));

        // Monthly revenue
        $monthlyRevenue = DB::table('transactions')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', '!=', 'Refunded')
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('M'))
            ->map(fn($month) => $month->sum('total'));

        // Quarterly revenue
        $quarterlyRevenue = DB::table('transactions')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', '!=', 'Refunded')
            ->get()
            ->groupBy(fn($t) => 'Q' . ceil(Carbon::parse($t->created_at)->month / 3))
            ->map(fn($q) => $q->sum('total'));

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

    // Updated dashboardData method with better debugging
    public function dashboardData()
    {
        // Today's revenue: total from transactions (don't double-count advance_payment)
        $todayRevenue = DB::table('transactions')
            ->whereDate('created_at', Carbon::today())
            ->where('status', '!=', 'Refunded')
            ->sum('total');

        // Today's customers: sum of pax from reservations  
        $todayCustomers = DB::table('reservations')
            ->whereDate('reservation_time', Carbon::today())
            ->whereIn('status', ['Accepted', 'Completed'])
            ->sum('pax');

        // Recent transactions with cashier info
        $transactions = DB::table('transactions')
            ->join('users', 'transactions.cashier_id', '=', 'users.id')
            ->select(
                'transactions.id',
                'transactions.total',
                'transactions.advance_payment',
                'transactions.created_at',
                'users.firstname',
                'users.lastname'
            )
            ->where('transactions.status', '!=', 'Refunded')
            ->when(
                DB::table('transactions')
                    ->whereDate('created_at', Carbon::today())
                    ->where('status', '!=', 'Refunded')
                    ->exists(),
                function ($query) {
                    return $query->whereDate('transactions.created_at', Carbon::today());
                },
                function ($query) {
                    // If no transactions today, show recent ones
                    return $query->where('transactions.created_at', '>=', Carbon::now()->subDays(7));
                }
            )
            ->orderBy('transactions.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'cashier' => [
                        'firstname' => $t->firstname,
                        'lastname' => $t->lastname,
                    ],
                    'total_amount' => $t->total,
                    'created_at' => $t->created_at,
                    'is_today' => Carbon::parse($t->created_at)->isToday(),
                ];
            });

        // Stock data
        $stockData = DB::table('stock')
            ->whereNull('deleted_at')
            ->select('stock_name as name', 'stock_quantity as quantity')
            ->get();

        return response()->json([
            'revenue' => number_format($todayRevenue, 2),
            'customers' => $todayCustomers,
            'transactions' => $transactions,
            'stock' => $stockData,
            'debug' => [
                'today_date' => Carbon::today()->toDateString(),
                'transaction_count_today' => DB::table('transactions')
                    ->whereDate('created_at', Carbon::today())
                    ->where('status', '!=', 'Refunded')
                    ->count(),
                'transaction_count_total' => DB::table('transactions')->count(),
                'showing_recent_instead' => $transactions->where('is_today', false)->count() > 0,
            ]
        ]);
    }

    public function salesData()
    {
        // Weekly revenue
        $weeklyRevenue = DB::table('transactions')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->where('status', '!=', 'Refunded')
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('D'))
            ->map(fn($day) => $day->sum('total'));

        // Monthly revenue
        $monthlyRevenue = DB::table('transactions')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', '!=', 'Refunded')
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('M'))
            ->map(fn($month) => $month->sum('total'));

        // Quarterly revenue
        $quarterlyRevenue = DB::table('transactions')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', '!=', 'Refunded')
            ->get()
            ->groupBy(fn($t) => 'Q' . ceil(Carbon::parse($t->created_at)->month / 3))
            ->map(fn($q) => $q->sum('total'));

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

    public function storeMenu(Request $request)
    {
        try {
            $request->validate([
                'menu_item' => 'required|string|max:255|unique:menu,menu_item,NULL,id,deleted_at,NULL',
                'category' => 'required|in:main,add_ons',
                'regular_price' => 'required|numeric|min:0',
                'student_price' => 'nullable|numeric|min:0',
                'govt_employee_price' => 'nullable|numeric|min:0',
            ]);

            $existingMenu = DB::table('menu')
                ->where('menu_item', $request->menu_item)
                ->whereNull('deleted_at')
                ->first();

            if ($existingMenu) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['menu_item' => 'This menu item already exists in the active menu list.']);
            }

            DB::table('menu')->insert([
                'menu_item' => $request->menu_item,
                'category' => $request->category,
                'regular_price' => $request->regular_price,
                'student_price' => $request->student_price,
                'govt_employee_price' => $request->govt_employee_price,
                'has_customer_discount' => false,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.menu_management')
                ->with('success', 'Menu item added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while adding the menu item. Please try again.');
        }
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
        try {
            $request->validate([
                'menu_item' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:menu,menu_item,' . $id . ',id,deleted_at,NULL'
                ],
                'regular_price' => 'required|numeric|min:0',
                'student_price' => 'nullable|numeric|min:0',
                'govt_employee_price' => 'nullable|numeric|min:0',
                'category' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'menu_item.required' => 'Menu item name is required.',
                'menu_item.unique' => 'This menu item name is already taken by another item.',
                'menu_item.max' => 'Menu item name cannot exceed 255 characters.',
                'regular_price.required' => 'Regular price is required.',
                'regular_price.numeric' => 'Regular price must be a valid number.',
                'regular_price.min' => 'Regular price cannot be negative.',
                'student_price.numeric' => 'Student price must be a valid number.',
                'student_price.min' => 'Student price cannot be negative.',
                'govt_employee_price.numeric' => 'Government employee price must be a valid number.',
                'govt_employee_price.min' => 'Government employee price cannot be negative.',
                'category.required' => 'Category is required.',
                'category.max' => 'Category cannot exceed 255 characters.',
                'image.image' => 'The uploaded file must be an image.',
                'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif.',
                'image.max' => 'Image size cannot exceed 2MB.',
            ]);

            // Prepare update data
            $updateData = [
                'menu_item' => $request->menu_item,
                'regular_price' => $request->regular_price,
                'student_price' => $request->student_price,
                'govt_employee_price' => $request->govt_employee_price,
                'category' => $request->category,
                'updated_at' => now(),
            ];

            // Update has_customer_discount based on discount prices
            $updateData['has_customer_discount'] = !empty($request->student_price) || !empty($request->govt_employee_price);

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                // Get the current menu item to delete old image
                $currentMenu = DB::table('menu')->where('id', $id)->first();

                // Delete old image if it exists
                if ($currentMenu && $currentMenu->image) {
                    $oldImagePath = public_path('assets/jeongol-menu/' . $currentMenu->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Upload new image
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('assets/jeongol-menu'), $imageName);
                $updateData['image'] = $imageName;
            }

            // Update the menu item
            DB::table('menu')->where('id', $id)->update($updateData);

            return redirect()->route('admin.menu_management')
                ->with('success', 'Menu item updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please check the form for errors.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the menu item: ' . $e->getMessage());
        }
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

        $conflictingItem = DB::table('menu')
            ->where('menu_item', $menuItem->menu_item)
            ->whereNull('deleted_at')
            ->first();

        if ($conflictingItem) {
            return redirect()->back()->with('error', 'Cannot restore: A menu item with this name already exists in the active list!');
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
            $tables = DB::table('tables')
                ->whereNotNull('deleted_at')
                ->get();
        } else {
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
            $stocks = DB::table('stock')
                ->whereNotNull('deleted_at')
                ->get();
        } else {
            $stocks = DB::table('stock')
                ->whereNull('deleted_at')
                ->get();
        }

        return view('admin.stock_management', compact('stocks'));
    }

    public function storeStock(Request $request)
    {
        try {
            $request->validate([
                'stock_name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:stock,stock_name,NULL,id,deleted_at,NULL'
                ],
                'stock_quantity' => 'required|numeric|min:0',
            ], [
                'stock_name.required' => 'Stock name is required.',
                'stock_name.unique' => 'This stock item already exists in the active stock list.',
                'stock_name.max' => 'Stock name cannot exceed 255 characters.',
                'stock_quantity.required' => 'Stock quantity is required.',
                'stock_quantity.numeric' => 'Stock quantity must be a valid number.',
                'stock_quantity.min' => 'Stock quantity cannot be negative.',
            ]);

            // Check for existing stock item
            $existingStock = DB::table('stock')
                ->where('stock_name', $request->stock_name)
                ->whereNull('deleted_at')
                ->first();

            if ($existingStock) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['stock_name' => 'This stock item already exists in the active stock list.']);
            }

            DB::table('stock')->insert([
                'stock_name' => $request->stock_name,
                'stock_quantity' => $request->stock_quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.stock_management')
                ->with('success', 'Stock item added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while adding the stock item. Please try again.');
        }
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
        try {
            $request->validate([
                'stock_name' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:stock,stock_name,' . $id . ',id,deleted_at,NULL'
                ],
                'stock_quantity' => 'required|numeric|min:0',
            ], [
                'stock_name.required' => 'Stock name is required.',
                'stock_name.unique' => 'This stock name is already taken by another item.',
                'stock_name.max' => 'Stock name cannot exceed 255 characters.',
                'stock_quantity.required' => 'Stock quantity is required.',
                'stock_quantity.numeric' => 'Stock quantity must be a valid number.',
                'stock_quantity.min' => 'Stock quantity cannot be negative.',
            ]);

            DB::table('stock')->where('id', $id)->update([
                'stock_name' => $request->stock_name,
                'stock_quantity' => $request->stock_quantity,
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.stock_management')
                ->with('success', 'Stock item updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the stock item.');
        }
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

        // Check if stock name conflicts with existing active items
        $conflictingStock = DB::table('stock')
            ->where('stock_name', $stockItem->stock_name)
            ->whereNull('deleted_at')
            ->first();

        if ($conflictingStock) {
            return redirect()->back()->with('error', 'Cannot restore: A stock item with this name already exists in the active list!');
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

        // Fixed: Filter out refunded transactions and handle null timestamps
        $totalSales = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total') ?? 0;

        // Fixed: Use completed reservations instead of just accepted
        $totalPax = DB::table('reservations')
            ->whereIn('status', ['Accepted', 'Completed'])
            ->whereBetween('reservation_time', [$dateFrom, $dateTo])
            ->sum('pax') ?? 0;

        // Fixed: Use discount_total column from transactions table
        $totalDiscounts = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('discount_total') ?? 0;

        // Fixed: Get product consumption from transaction_details instead of order_details
        $productConsumption = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('order_details', 'transaction_details.order_detail_id', '=', 'order_details.id')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->whereNotNull('transactions.created_at')
            ->where('transactions.status', '!=', 'Refunded')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                'menu.menu_item',
                'menu.category',
                DB::raw('SUM(transaction_details.quantity) as total_quantity'),
                DB::raw('SUM((menu.regular_price - COALESCE(transaction_details.discount_amount, 0)) * transaction_details.quantity) as total_revenue')
            )
            ->groupBy('menu.id', 'menu.menu_item', 'menu.category')
            ->orderByDesc('total_quantity')
            ->get();

        // Fixed: Added proper column references and null handling
        $sales = DB::table('transactions')
            ->join('reservations', 'transactions.reservation_id', '=', 'reservations.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->whereNotNull('transactions.created_at')
            ->where('transactions.status', '!=', 'Refunded')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                'transactions.id as id',
                'transactions.transaction_no',
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('TIME(transactions.created_at) as time'),
                DB::raw('COALESCE(tables.table_number, "N/A") as table_number'),
                DB::raw('COALESCE(customers.name, "Walk-in") as customer_name'),
                'reservations.pax',
                'transactions.subtotal',
                'transactions.discount_total',
                'transactions.total',
                'transactions.payment_method'
            )
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        // Fixed: Handle null timestamps
        $salesTrend = DB::table('transactions')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Additional metrics for better reporting
        $transactionCount = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $averageOrderValue = $transactionCount > 0 ? $totalSales / $transactionCount : 0;

        $topSellingItems = $productConsumption->take(10);

        return view('admin.reports', compact(
            'dateFrom',
            'dateTo',
            'totalSales',
            'totalPax',
            'totalDiscounts',
            'productConsumption',
            'sales',
            'salesTrend',
            'transactionCount',
            'averageOrderValue',
            'topSellingItems'
        ));
    }

    public function export(Request $request)
    {
        $fromInput = $request->input('from_date');
        $toInput   = $request->input('to_date');

        $dateFrom = $fromInput ? Carbon::parse($fromInput)->startOfDay() : Carbon::now()->startOfDay();
        $dateTo   = $toInput   ? Carbon::parse($toInput)->endOfDay()     : Carbon::now()->endOfDay();

        $totalSales = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total') ?? 0;

        $totalPax = DB::table('reservations')
            ->whereIn('status', ['Accepted', 'Completed'])
            ->whereBetween('reservation_time', [$dateFrom, $dateTo])
            ->sum('pax') ?? 0;

        $totalDiscounts = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('discount_total') ?? 0;

        $productConsumption = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('order_details', 'transaction_details.order_detail_id', '=', 'order_details.id')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->whereNotNull('transactions.created_at')
            ->where('transactions.status', '!=', 'Refunded')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                'menu.menu_item',
                'menu.category',
                DB::raw('SUM(transaction_details.quantity) as total_quantity'),
                DB::raw('SUM((menu.regular_price - COALESCE(transaction_details.discount_amount, 0)) * transaction_details.quantity) as total_revenue')
            )
            ->groupBy('menu.id', 'menu.menu_item', 'menu.category')
            ->orderByDesc('total_quantity')
            ->get();

        $sales = DB::table('transactions')
            ->join('reservations', 'transactions.reservation_id', '=', 'reservations.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->whereNotNull('transactions.created_at')
            ->where('transactions.status', '!=', 'Refunded')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                'transactions.transaction_no',
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('TIME(transactions.created_at) as time'),
                DB::raw('COALESCE(tables.table_number, "N/A") as table_number'),
                DB::raw('COALESCE(customers.name, "Walk-in") as customer_name'),
                'reservations.pax',
                'transactions.subtotal',
                'transactions.discount_total',
                'transactions.total',
                'transactions.payment_method'
            )
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        $transactionCount = $sales->count();
        $averageOrderValue = $transactionCount > 0 ? $totalSales / $transactionCount : 0;

        $data = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalSales' => $totalSales,
            'totalPax' => $totalPax,
            'totalDiscounts' => $totalDiscounts,
            'transactionCount' => $transactionCount,
            'averageOrderValue' => $averageOrderValue,
            'productConsumption' => $productConsumption,
            'sales' => $sales,
            'generatedAt' => now(),
        ];

        $pdf = Pdf::loadView('admin.reports.pdf-export', $data);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'sales-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    public function exportCsv(Request $request)
    {
        $fromInput = $request->input('from_date');
        $toInput   = $request->input('to_date');

        $dateFrom = $fromInput ? Carbon::parse($fromInput)->startOfDay() : Carbon::now()->startOfDay();
        $dateTo   = $toInput   ? Carbon::parse($toInput)->endOfDay()     : Carbon::now()->endOfDay();

        $totalSales = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total') ?? 0;

        $totalPax = DB::table('reservations')
            ->whereIn('status', ['Accepted', 'Completed'])
            ->whereBetween('reservation_time', [$dateFrom, $dateTo])
            ->sum('pax') ?? 0;

        $totalDiscounts = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('discount_total') ?? 0;

        $productConsumption = DB::table('transaction_details')
            ->join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->join('order_details', 'transaction_details.order_detail_id', '=', 'order_details.id')
            ->join('menu', 'order_details.menu_id', '=', 'menu.id')
            ->whereNotNull('transactions.created_at')
            ->where('transactions.status', '!=', 'Refunded')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                'menu.menu_item',
                'menu.category',
                DB::raw('SUM(transaction_details.quantity) as total_quantity'),
                DB::raw('SUM((menu.regular_price - COALESCE(transaction_details.discount_amount, 0)) * transaction_details.quantity) as total_revenue')
            )
            ->groupBy('menu.id', 'menu.menu_item', 'menu.category')
            ->orderByDesc('total_quantity')
            ->get();

        $sales = DB::table('transactions')
            ->join('reservations', 'transactions.reservation_id', '=', 'reservations.id')
            ->leftJoin('tables', 'reservations.table_id', '=', 'tables.id')
            ->leftJoin('customers', 'transactions.customer_id', '=', 'customers.id')
            ->whereNotNull('transactions.created_at')
            ->where('transactions.status', '!=', 'Refunded')
            ->whereBetween('transactions.created_at', [$dateFrom, $dateTo])
            ->select(
                'transactions.transaction_no',
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('COALESCE(tables.table_number, "N/A") as table_number'),
                DB::raw('COALESCE(customers.name, "Walk-in") as customer_name'),
                'reservations.pax',
                'transactions.subtotal',
                'transactions.discount_total',
                'transactions.total'
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
            fputcsv($file, ['Total Sales', '₱' . number_format($totalSales, 2)]);
            fputcsv($file, ['Total Pax', $totalPax]);
            fputcsv($file, ['Total Discounts', '₱' . number_format($totalDiscounts, 2)]);
            fputcsv($file, []);

            fputcsv($file, ['PRODUCT CONSUMPTION']);
            fputcsv($file, ['Product', 'Category', 'Quantity', 'Revenue']);
            foreach ($productConsumption as $product) {
                fputcsv($file, [
                    $product->menu_item,
                    ucfirst($product->category),
                    $product->total_quantity,
                    '₱' . number_format($product->total_revenue, 2)
                ]);
            }
            fputcsv($file, []);

            fputcsv($file, ['SALES BREAKDOWN']);
            fputcsv($file, ['Transaction #', 'Date', 'Table', 'Customer', 'Pax', 'Subtotal', 'Discount', 'Total']);
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->transaction_no ?? '#' . $sale->id,
                    $sale->date,
                    $sale->table_number,
                    $sale->customer_name,
                    $sale->pax,
                    '₱' . number_format($sale->subtotal, 2),
                    '₱' . number_format($sale->discount_total ?? 0, 2),
                    '₱' . number_format($sale->total, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function ewallet_management()
    {
        $ewallet_details = DB::table('ewallet_details')->get();
        return view('admin.ewallet_management', compact('ewallet_details'));
    }

    public function ewallet_store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:gcash,maya',
            'wallet_name'    => 'required|string|max:255',
            'wallet_number'  => 'required|string|max:50',
        ]);

        DB::table('ewallet_details')
            ->where('payment_method', $request->payment_method)
            ->update(['is_active' => false]);

        DB::table('ewallet_details')->insert([
            'payment_method' => $request->payment_method,
            'wallet_name'    => $request->wallet_name,
            'wallet_number'  => $request->wallet_number,
            'is_active'      => true,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return redirect()->back()->with('success', ucfirst($request->payment_method) . ' wallet is now the active one!');
    }

    public function activate($id)
    {
        $wallet = DB::table('ewallet_details')->where('id', $id)->first();

        if (!$wallet) {
            return redirect()->back()->with('error', 'Wallet not found!');
        }

        DB::table('ewallet_details')
            ->where('payment_method', $wallet->payment_method)
            ->update(['is_active' => false]);

        DB::table('ewallet_details')
            ->where('id', $id)
            ->update(['is_active' => true]);

        return redirect()->back()->with('success', ucfirst($wallet->payment_method) . ' wallet set to Active!');
    }

    public function deactivate($id)
    {
        $wallet = DB::table('ewallet_details')->where('id', $id)->first();

        if (!$wallet) {
            return redirect()->back()->with('error', 'Wallet not found!');
        }

        DB::table('ewallet_details')
            ->where('id', $id)
            ->update(['is_active' => false]);

        return redirect()->back()->with('success', ucfirst($wallet->payment_method) . ' wallet set to Inactive!');
    }



    public function feedback()
    {
        $feedbacks = DB::table('feedback')->get();
        return view('admin.feedback', compact('feedbacks'));
    }
}
