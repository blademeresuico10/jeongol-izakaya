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
use App\Models\reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\menu;
use App\Models\orders;
use Illuminate\Support\Facades\Log;
use App\Models\expiredIngredients;
use App\Models\ingredientBatch;
use App\Models\ingredientMovements;
use App\Models\ingredients;
use App\Models\walkin;
use App\Models\OperatingHour;
use App\Models\MenuDiscount;
use App\Models\StockAlertLevel;
use App\Models\StockOrder;

class AdminController extends Controller
{
    public function home()
    {
        $today = Carbon::today();

        $totalGrossSales = transaction::whereDate('created_at', $today)->sum('grand_total');

        $todayReservationOrders = orders::whereDate('created_at', $today)
            ->whereNotNull('reservation_id')
            ->distinct('reservation_id')
            ->count('reservation_id');

        $todayWalkinOrders = orders::whereDate('created_at', $today)
            ->whereNotNull('walk_in_id')
            ->distinct('walk_in_id')
            ->count('walk_in_id');

        $totalOrders = $todayReservationOrders + $todayWalkinOrders;

        $reservationPax = Reservation::whereDate('created_at', $today)
            ->whereHas('orders')
            ->sum('pax');

        $walkinPax = walkin::whereDate('created_at', $today)
            ->whereHas('orders')
            ->sum('pax');

        $totalCustomers = $reservationPax + $walkinPax;

        $totalReservations = Reservation::whereDate('created_at', $today)->count();

        $yesterday = Carbon::yesterday();

        $todaySales = $totalGrossSales;
        $yesterdaySales = transaction::whereDate('created_at', $yesterday)->sum('grand_total');
        $salesChange = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;

        $yesterdayReservationOrders = orders::whereDate('created_at', $yesterday)
            ->whereNotNull('reservation_id')
            ->distinct('reservation_id')
            ->count('reservation_id');

        $yesterdayWalkinOrders = orders::whereDate('created_at', $yesterday)
            ->whereNotNull('walk_in_id')
            ->distinct('walk_in_id')
            ->count('walk_in_id');

        $yesterdayOrders = $yesterdayReservationOrders + $yesterdayWalkinOrders;
        $ordersChange = $yesterdayOrders > 0 ? (($totalOrders - $yesterdayOrders) / $yesterdayOrders) * 100 : 0;

        $yesterdayResPax = Reservation::whereDate('created_at', $yesterday)
            ->whereHas('orders')
            ->sum('pax');

        $yesterdayWalkinPax = walkin::whereDate('created_at', $yesterday)
            ->whereHas('orders')
            ->sum('pax');

        $yesterdayCustomers = $yesterdayResPax + $yesterdayWalkinPax;
        $customersChange = $yesterdayCustomers > 0 ? (($totalCustomers - $yesterdayCustomers) / $yesterdayCustomers) * 100 : 0;

        $yesterdayReservations = Reservation::whereDate('created_at', $yesterday)->count();
        $reservationsChange = $yesterdayReservations > 0 ? (($totalReservations - $yesterdayReservations) / $yesterdayReservations) * 100 : 0;

        $startDate = Carbon::now()->subDays(6);
        $endDate = Carbon::now();

        $salesTrend = transaction::selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $ordersTrend = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            $resOrders = orders::whereDate('created_at', $date)
                ->whereNotNull('reservation_id')
                ->distinct('reservation_id')
                ->count('reservation_id');

            $walkinOrders = orders::whereDate('created_at', $date)
                ->whereNotNull('walk_in_id')
                ->distinct('walk_in_id')
                ->count('walk_in_id');

            $ordersTrend->put($date, $resOrders + $walkinOrders);
        }
        $ordersTrend = $ordersTrend->sortKeys();

        $customersTrend = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            $resPax = Reservation::whereDate('created_at', $date)
                ->whereHas('orders')
                ->sum('pax');

            $walkinPax = walkin::whereDate('created_at', $date)
                ->whereHas('orders')
                ->sum('pax');

            $customersTrend->put($date, $resPax + $walkinPax);
        }
        $customersTrend = $customersTrend->sortKeys();

        $reservationsTrend = Reservation::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $currentYear = Carbon::now()->year;
        $monthlySales = transaction::selectRaw('MONTH(created_at) as month, SUM(grand_total) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlySalesData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySalesData[] = $monthlySales->get($i, 0);
        }

        $flagshipItems = orders::join('menu', 'orders.menu_id', '=', 'menu.id')
            ->select('menu.menu_item', 'menu.image', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->groupBy('menu.menu_item', 'menu.image')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $recentReservations = Reservation::whereDate('created_at', $today)
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'type' => 'Reservation',
                    'created_at' => $r->created_at,
                    'time' => $r->created_at->diffForHumans(),
                    'icon' => 'fa-calendar-check',
                    'color' => '#4ade80',
                ];
            });

        $recentWalkins = walkin::with(['customer', 'table'])
            ->whereDate('created_at', $today)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Walk-in',
                    'name' => optional($item->customer)->name ?? 'Guest',
                    'table' => optional($item->table)->table_name ?? 'N/A',
                    'status' => ucfirst($item->status),
                    'created_at' => $item->created_at,
                    'time' => $item->created_at->diffForHumans(),
                    'icon' => 'fa-user-check',
                    'color' => '#60a5fa',
                ];
            });

        $recentTransactions = transaction::with(['customer'])
            ->whereDate('created_at', $today)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Transaction',
                    'name' => optional($item->customer)->name ?? 'Guest',
                    'table' => null,
                    'status' => ucfirst($item->status),
                    'created_at' => $item->created_at,
                    'time' => $item->created_at->diffForHumans(),
                    'icon' => 'fa-receipt',
                    'color' => '#facc15',
                ];
            });

        $recentActivities = collect()
            ->merge($recentReservations)
            ->merge($recentWalkins)
            ->merge($recentTransactions)
            ->sortByDesc('created_at')
            ->take(7)
            ->values();

        $ingredients = ingredients::select('id', 'name', 'category', 'unit', 'stocks')
            ->with(['stockAlertLevel'])
            ->get()
            ->map(function ($ingredient) {
                $alertLevel = $ingredient->stockAlertLevel;

                if ($alertLevel) {
                    if ($ingredient->stocks <= ($alertLevel->critical_stock ?? 0)) {
                        $ingredient->status = 'critical';
                        $ingredient->badge_class = 'bg-danger';
                        $ingredient->badge_text = 'Critical';
                        $ingredient->badge_icon = 'fa-exclamation-triangle';
                        $ingredient->sort_order = 1;
                    } elseif ($ingredient->stocks <= ($alertLevel->low_stock ?? 0)) {
                        $ingredient->status = 'low';
                        $ingredient->badge_class = 'bg-warning';
                        $ingredient->badge_text = 'Low Stock';
                        $ingredient->badge_icon = 'fa-exclamation-circle';
                        $ingredient->sort_order = 2;
                    } else {
                        $ingredient->status = 'good';
                        $ingredient->badge_class = 'bg-success';
                        $ingredient->badge_text = 'Good';
                        $ingredient->badge_icon = 'fa-check-circle';
                        $ingredient->sort_order = 3;
                    }
                } else {
                    if ($ingredient->stocks < 10) {
                        $ingredient->status = 'low';
                        $ingredient->badge_class = 'bg-danger';
                        $ingredient->badge_text = 'Low Stock';
                        $ingredient->badge_icon = 'fa-exclamation-triangle';
                        $ingredient->sort_order = 1;
                    } elseif ($ingredient->stocks < 50) {
                        $ingredient->status = 'medium';
                        $ingredient->badge_class = 'bg-warning';
                        $ingredient->badge_text = 'Medium';
                        $ingredient->badge_icon = 'fa-exclamation-circle';
                        $ingredient->sort_order = 2;
                    } else {
                        $ingredient->status = 'good';
                        $ingredient->badge_class = 'bg-success';
                        $ingredient->badge_text = 'Good';
                        $ingredient->badge_icon = 'fa-check-circle';
                        $ingredient->sort_order = 3;
                    }
                }

                return $ingredient;
            })
            ->sortBy('sort_order')
            ->values();

        return view('admin.home', compact(
            'totalGrossSales',
            'totalOrders',
            'totalCustomers',
            'totalReservations',
            'salesChange',
            'ordersChange',
            'customersChange',
            'reservationsChange',
            'salesTrend',
            'ordersTrend',
            'customersTrend',
            'reservationsTrend',
            'monthlySalesData',
            'flagshipItems',
            'recentActivities',
            'ingredients'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('admin.myprofile', compact('user'));
    }

    public function updateProfile(Request $request, $id)
    {
        try {
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'email' => 'required|email|unique:users,email,' . $id,
                'address' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username,' . $id,
                'password' => 'nullable|string|min:6',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $user = User::findOrFail($id);

            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->contact_number = $request->contact_number;
            $user->address = $request->address;
            $user->email = $request->email;
            $user->username = $request->username;

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
            }

            return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating profile'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update profile');
        }
    }

    public function changePassword(Request $request, $id)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);

            $user = User::findOrFail($id);

            if (!Hash::check($request->current_password, $user->password)) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current password is incorrect'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Current password is incorrect');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.profile');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while changing password'
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to change password');
        }
    }

    public function users()
    {
        if (request()->has('show_deleted')) {
            $users = User::onlyTrashed()->where('role', '!=', 'Admin')->get();
        } else {
            $users = User::where('role', '!=', 'Admin')->get();
        }

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        try {
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'role' => 'required|string|in:Admin,Receptionist,Cashier,Kitchen Staff',
                'contact_number' => 'required|string|max:11',
                'address' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username',
                'email' => 'nullable|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('storage/jeongol_menu'), $imageName);
        }

        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'role' => $request->role,
            'contact_number' => $request->contact_number,
            'username' => $request->username,
            'email' => $request->email,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'profile_picture' => $request->hasFile('profile_picture') ? $request->file('profile_picture')->store('profile_pictures', 'public') : null,
            'status' => $request->has('status') ? 'Active' : 'Inactive',
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User added successfully!']);
        }

        return redirect()->route('admin.users')->with('success', 'User added successfully!');
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'role' => 'required|string',
                'contact_number' => 'required|string|max:20',
                'email' => 'required|email|unique:users,email,' . $id,
                'address' => 'required|string|max:255',
                'username' => 'required|string|unique:users,username,' . $id,
                'password' => 'nullable|string|min:6',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $user = User::findOrFail($id);

        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->role = $request->role;
        $user->contact_number = $request->contact_number;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->username = $request->username;
        $user->status = $request->has('status') ? 'Active' : 'Inactive';

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $user->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'User updated successfully!']);
        }

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->role === 'admin') {
                return redirect()->route('admin.users')->with('error', 'Cannot delete admin user!');
            }

            $user->delete();

            $user->status = 'Deleted';
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();

            return redirect()->route('admin.users')->with('success', 'User restored successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Error restoring user: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);

            if ($user->role === 'admin') {
                return redirect()->route('admin.users')->with('error', 'Cannot permanently delete admin user!');
            }

            $user->forceDelete();

            return redirect()->route('admin.users', ['show_deleted' => true])->with('success', 'User permanently deleted!');
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Error permanently deleting user: ' . $e->getMessage());
        }
    }

    public function menu_management(Request $request)
    {
        $showDeleted = $request->has('show_deleted');

        if ($showDeleted) {
            $menu = Menu::onlyTrashed()->get();
        } else {
            $menu = Menu::all();
        }

        return view('admin.menu_management', compact('menu'));
    }

    public function menuIngredients()
    {
        try {
            $menus = DB::table('menu')
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->get();

            $ingredients = [];
            foreach ($menus as $menu) {
                $ingredients[$menu->id] = DB::table('menu_ingredients as mi')
                    ->join('ingredients as i', 'mi.ingredient_id', '=', 'i.id')
                    ->where('mi.menu_id', $menu->id)
                    ->select(
                        'mi.id',
                        'mi.quantity',
                        'i.name as ingredient_name',
                        'i.category',
                        'i.unit',
                        'i.stocks as stock'
                    )
                    ->get();
            }

            return response()->json([
                'success' => true,
                'menus' => $menus,
                'ingredients' => $ingredients
            ]);
        } catch (\Exception $e) {
            Log::error('Menu ingredients fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch menu ingredients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getIngredients($id)
    {
        $menu = menu::findOrFail($id);

        $ingredients = DB::table('menu_ingredients')
            ->join('ingredients', 'menu_ingredients.ingredient_id', '=', 'ingredients.id')
            ->where('menu_ingredients.menu_id', $menu->id)
            ->select(
                'menu_ingredients.id',
                'menu_ingredients.menu_id',
                'menu_ingredients.quantity',
                'ingredients.name as ingredient_name'
            )
            ->get();

        return response()->json([
            'menu' => $menu,
            'ingredients' => $ingredients
        ]);
    }

    public function attachIngredient(Request $request, $menuId)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:1'
        ]);

        $exists = DB::table('menu_ingredients')
            ->where('menu_id', $menuId)
            ->where('ingredient_id', $request->ingredient_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This ingredient is already in this menu. Please update the quantity instead.'
            ], 422);
        }

        try {
            DB::table('menu_ingredients')->insert([
                'menu_id' => $menuId,
                'ingredient_id' => $request->ingredient_id,
                'quantity' => $request->quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ingredient added to menu!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add ingredient: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateMenuIngredients(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:menu_ingredients,id',
            'updates.*.quantity' => 'required|numeric|min:0'
        ]);

        foreach ($request->updates as $update) {
            DB::table('menu_ingredients')
                ->where('id', $update['id'])
                ->update([
                    'quantity' => $update['quantity'],
                    'updated_at' => now()
                ]);
        }

        return response()->json(['success' => true]);
    }

    public function getAllIngredients()
    {
        $ingredients = DB::table('ingredients')
            ->select('id', 'name', 'category', 'unit', 'stocks')
            ->orderBy('category')
            ->get();

        return response()->json([
            'ingredients' => $ingredients
        ]);
    }

    public function removeMenuIngredient($id)
    {
        try {
            DB::table('menu_ingredients')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ingredient removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove ingredient'
            ], 500);
        }
    }

    public function expiryData()
    {
        try {

            $allBatches = DB::table('ingredient_batches')->count();

            $sevenDaysFromNow = now()->addDays(7);

            $batches = DB::table('ingredient_batches')
                ->join('ingredients', 'ingredient_batches.ingredient_id', '=', 'ingredients.id')
                ->select(
                    'ingredient_batches.id',
                    'ingredient_batches.ingredient_id',
                    'ingredient_batches.quantity',
                    'ingredient_batches.expiration_date',
                    'ingredients.name as ingredient_name',
                    'ingredients.unit'
                )
                ->where('ingredient_batches.quantity', '>', 0)
                ->whereDate('ingredient_batches.expiration_date', '<=', $sevenDaysFromNow)
                ->orderBy('ingredient_batches.expiration_date', 'asc')
                ->get();


            $processedBatches = $batches->map(function ($batch) {
                $expiryDate = \Carbon\Carbon::parse($batch->expiration_date);
                $today = \Carbon\Carbon::today();


                if ($expiryDate->isPast()) {
                    $batch->status = 'expired';
                    $batch->days_difference = $today->diffInDays($expiryDate);
                } elseif ($expiryDate->diffInDays($today) <= 3) {
                    $batch->status = 'expiring_soon';
                    $batch->days_difference = $expiryDate->diffInDays($today);
                } else {
                    $batch->status = 'expiring_this_week';
                    $batch->days_difference = $expiryDate->diffInDays($today);
                }

                return $batch;
            });

            return response()->json(['expiry_data' => $processedBatches]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load expiry data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:ingredient_batches,id'
        ]);

        try {
            DB::beginTransaction();

            $batch = ingredientBatch::with('ingredient')->lockForUpdate()->find($request->batch_id);

            if (!$batch) {
                return response()->json(['success' => false, 'message' => 'Batch not found'], 404);
            }

            if ($batch->quantity <= 0) {
                return response()->json(['success' => false, 'message' => 'Batch already has zero quantity'], 400);
            }

            $ingredient   = $batch->ingredient;
            $stockBefore  = $ingredient->stocks;
            $stockAfter   = max(0, $stockBefore - $batch->quantity);

            expiredIngredients::create([
                'ingredient_id'   => $ingredient->id,
                'ingredient_name' => $ingredient->name,
                'category'        => $ingredient->category,
                'quantity'        => $batch->quantity,
                'unit'            => $ingredient->unit,
                'expiration_date' => $batch->expiration_date,
                'expired_at'      => now(),
                'notes'           => 'Batch removed'
            ]);

            $ingredient->update(['stocks' => $stockAfter]);

            ingredientMovements::create([
                'ingredient_id' => $ingredient->id,
                'user_id'       => Auth::id(),
                'type'          => 'expired',
                'quantity'      => -$batch->quantity,
                'stock_before'  => $stockBefore,
                'stock_after'   => $stockAfter,
                'notes'         => "Expired on {$batch->expiration_date}"
            ]);

            $batch->update(['quantity' => 0]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Batch marked as expired and quantity set to zero'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove batch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getExpiredHistory()
    {
        $expired = DB::table('expired_ingredients')
            ->orderBy('expired_at', 'desc')
            ->get();

        return response()->json(['expired' => $expired]);
    }

    public function storeMenu(Request $request)
    {
        try {
            $request->validate(
                [
                    'menu_item' => 'required|string|max:255|unique:menu,menu_item,NULL,id,deleted_at,NULL',
                    'category' => 'required|in:main,add_ons',
                    'regular_price' => 'required|numeric|min:0',
                    'has_customer_discount' => 'required|boolean',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ],
                [
                    'menu_item.required' => 'Menu item name is required.',
                    'menu_item.unique' => 'The menu already exists.',
                    'menu_item.max' => 'Menu item name cannot exceed 255 characters.',
                    'category.required' => 'Category is required.',
                    'category.in' => 'Category must be either Main or Add-ons.',
                    'regular_price.required' => 'Regular price is required.',
                    'regular_price.numeric' => 'Regular price must be a valid number.',
                    'regular_price.min' => 'Regular price cannot be negative.',
                    'has_customer_discount.required' => 'Customer discount option is required.',
                    'image.required' => 'Menu item image is required.',
                    'image.image' => 'The uploaded file must be an image.',
                    'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif.',
                    'image.max' => 'Image size cannot exceed 2MB.',
                ]
            );

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('storage/jeongol_menu'), $imageName);
            }

            $menu = Menu::create([
                'menu_item' => $request->menu_item,
                'category' => $request->category,
                'regular_price' => $request->regular_price,
                'has_customer_discount' => (bool) $request->has_customer_discount,
                'status' => 'Active',
                'image' => $imageName,
            ]);

            return redirect()->route('admin.menu_management')
                ->with('success', 'Menu item added successfully!')
                ->with('new_menu_name', $menu->menu_item);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator);
        } catch (\Exception $e) {
            Log::error('Menu store error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while adding the menu item: ' . $e->getMessage());
        }
    }

    private function getDefaultQuantity($unit, $menuCategory)
    {
        if ($unit === 'grams') {
            return $menuCategory === 'main' ? 100 : 500;
        } elseif ($unit === 'pieces') {
            return 1;
        }

        return 100;
    }

    private function getSuggestedIngredients($menuCategory)
    {
        // Define which ingredient categories to suggest for each menu category
        $categoryMapping = [
            'main' => ['meat', 'vegetables', 'soupbase'],  // Main dishes
            'add_ons' => ['meat', 'beverage'],             // Add-ons
        ];

        $ingredientCategories = $categoryMapping[$menuCategory] ?? [];

        if (empty($ingredientCategories)) {
            return [];
        }

        // Get all active ingredients from these categories
        $ingredients = DB::table('ingredients')
            ->whereIn('category', $ingredientCategories)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $suggested = [];
        foreach ($ingredients as $ingredient) {
            $suggested[] = [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'category' => $ingredient->category,
                'default_quantity' => $this->getDefaultQuantity($ingredient->unit, $menuCategory),
            ];
        }

        return $suggested;
    }

    public function getExistingIngredients($menuId)
    {
        try {
            $ingredientIds = DB::table('menu_ingredients')
                ->where('menu_id', $menuId)
                ->pluck('ingredient_id')
                ->toArray();

            return response()->json([
                'success' => true,
                'ingredient_ids' => $ingredientIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch existing ingredients',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSuggestedIngredientsApi($menuId)
    {
        try {
            $menu = DB::table('menu')->find($menuId);

            if (!$menu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu not found'
                ], 404);
            }

            $suggestions = $this->getSuggestedIngredients($menu->category);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions,
                'menu_category' => $menu->category
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch suggestions',
                'error' => $e->getMessage()
            ], 500);
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
                'status' => 'required|in:Active,Blocked',
                'category' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'status.required' => 'Status is required.',
                'status.in' => 'Status must be Active or Blocked.',
                'menu_item.required' => 'Menu item name is required.',
                'menu_item.unique' => 'This menu item name is already taken by another item.',
                'menu_item.max' => 'Menu item name cannot exceed 255 characters.',
                'regular_price.required' => 'Regular price is required.',
                'regular_price.numeric' => 'Regular price must be a valid number.',
                'regular_price.min' => 'Regular price cannot be negative.',
                'category.required' => 'Category is required.',
                'category.max' => 'Category cannot exceed 255 characters.',
                'image.image' => 'The uploaded file must be an image.',
                'image.mimes' => 'Image must be a file of type: jpeg, png, jpg, gif.',
                'image.max' => 'Image size cannot exceed 2MB.',
            ]);

            $menu = menu::findOrFail($id);

            if ($request->status === 'Blocked' && $menu->status !== 'Blocked') {
                $hasActiveOrders = orders::where('menu_id', $id)
                    ->whereHas('reservation', function ($query) {
                        $query->whereIn('status', ['pending', 'confirmed', 'in_progress']);
                    })
                    ->exists();

                if ($hasActiveOrders) {
                    return redirect()->back()->with(
                        'error',
                        'This Menu Item has active orders and cannot be blocked.'
                    );
                }
            }

            $updateData = [
                'menu_item' => $request->menu_item,
                'regular_price' => $request->regular_price,
                'category' => $request->category,
                'status' => $request->status,
                'updated_at' => now(),
            ];

            if ($request->hasFile('image')) {
                $currentMenu = DB::table('menu')->where('id', $id)->first();
                if ($currentMenu && $currentMenu->image) {
                    $oldImagePath = public_path('assets/jeongol-menu/' . $currentMenu->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('assets/jeongol-menu'), $imageName);
                $updateData['image'] = $imageName;
            }

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
        $tables = collect([]);
        $showDeleted = $request->has('show_deleted');

        $lastTableNumber = DB::table('tables')->max('table_number') ?? 0;
        $nextTableNumber = $lastTableNumber + 1;

        return view('admin.table_management', compact('tables', 'nextTableNumber', 'showDeleted'));
    }

    public function getTables(Request $request)
    {
        try {
            $showDeleted = $request->get('show_deleted', 'false');
            $showDeleted = ($showDeleted === 'true' || $showDeleted === true);

            $query = DB::table('tables');

            if ($showDeleted) {
                $query->whereNotNull('deleted_at');
            } else {
                $query->whereNull('deleted_at');
            }

            $tables = $query->orderBy('table_number', 'asc')
                ->paginate(10);

            $lastTableNumber = DB::table('tables')->max('table_number') ?? 0;
            $nextTableNumber = $lastTableNumber + 1;

            return response()->json([
                'tables' => $tables,
                'nextTableNumber' => $nextTableNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load tables: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addtable()
    {
        return view('admin.addtable');
    }

    public function storeTable(Request $request)
    {
        $request->validate([
            'capacity' => 'required|integer|min:1',
        ]);

        $nextTableNumber = $this->getNextTableNumber();

        DB::table('tables')->insert([
            'table_number' => $nextTableNumber,
            'capacity' => $request->capacity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.table_management')->with('success', 'Table added successfully with table number ' . $nextTableNumber . '!');
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
            'capacity' => 'required|integer|min:1',
        ]);

        DB::table('tables')->where('id', $id)->update([
            'capacity' => $request->capacity,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.table_management')->with('success', 'Table updated successfully!');
    }


    private function getNextTableNumber()
    {
        $lastTable = DB::table('tables')
            ->orderBy('table_number', 'desc')
            ->first();

        return $lastTable ? $lastTable->table_number + 1 : 1;
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

    public function ingredient_management()
    {
        $lowStockIngredients = ingredients::with('stockAlertLevel')
            ->whereHas('stockAlertLevel', function ($query) {
                $query->whereRaw('ingredients.stocks <= stock_level_alerts.low_stock');
            })
            ->get();

        $allIngredients = ingredients::with('stockAlertLevel')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.ingredient_management', compact('lowStockIngredients', 'allIngredients'));
    }

    public function createStockOrder(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01'
        ]);

        try {
            $order = StockOrder::createManualOrder(
                $request->ingredient_id,
                $request->quantity
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock order created successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create stock order: ' . $e->getMessage()
            ], 500);
        }
    }

    // Check all ingredients
    public function checkAllIngredients(Request $request)
    {
        $ingredients = ingredients::with('stockAlertLevel')->get();
        $ordersCreated = 0;

        foreach ($ingredients as $ingredient) {
            // Use the correct method name from your ingredients model
            $order = $ingredient->checkAndCreateStockOrder();

            if ($order) {
                $ordersCreated++;
            }
        }

        $message = $ordersCreated > 0
            ? "{$ordersCreated} stock order(s) created successfully"
            : "No ingredients need restocking at this time";

        return response()->json([
            'success' => true,
            'message' => $message,
            'orders_created' => $ordersCreated
        ]);
    }

    // Add this method for getting available ingredients (without pending orders)
    public function getAvailableIngredients()
    {
        $ingredients = ingredients::with('stockAlertLevel')
            ->whereDoesntHave('stockOrders', function ($query) {
                $query->where('status', 'pending');
            })
            ->orderBy('name')
            ->get()
            ->map(function ($ingredient) {
                // Determine stock status
                $stockStatus = 'normal';
                if ($ingredient->stockAlertLevel) {
                    if ($ingredient->stocks <= $ingredient->stockAlertLevel->critical_stock) {
                        $stockStatus = 'critical';
                    } elseif ($ingredient->stocks <= $ingredient->stockAlertLevel->low_stock) {
                        $stockStatus = 'low';
                    }
                }

                return [
                    'id' => $ingredient->id,
                    'name' => $ingredient->name,
                    'unit' => $ingredient->unit,
                    'stocks' => $ingredient->stocks,
                    'stock_status' => $stockStatus,
                    'reorder_quantity' => $ingredient->stockAlertLevel->reorder_quantity ?? 0,
                ];
            });

        return response()->json([
            'success' => true,
            'ingredients' => $ingredients
        ]);
    }

    // Complete stock order
    public function completeStockOrder(Request $request, StockOrder $stockOrder)
    {
        if ($stockOrder->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order is not pending'], 400);
        }

        $stockOrder->complete();

        return response()->json(['success' => true, 'message' => 'Stock order completed successfully']);
    }

    // Cancel stock order
    public function cancelStockOrder(Request $request, StockOrder $stockOrder)
    {
        if ($stockOrder->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order is not pending'], 400);
        }

        $stockOrder->cancel();

        return response()->json(['success' => true, 'message' => 'Stock order cancelled']);
    }

    public function getStockOrders()
    {
        $stockOrders = StockOrder::with('ingredient')->latest()->paginate(10);
        return response()->json([
            'stock_orders' => $stockOrders
        ]);
    }

    public function getLowStockIngredients()
    {
        $ingredients = Ingredients::with('stockAlertLevel')->get();

        $lowStockIngredients = $ingredients->filter(function ($ingredient) {
            return $ingredient->stockAlertLevel && $ingredient->stocks <= $ingredient->stockAlertLevel->low_stock;
        })->values();

        return response()->json([
            'low_stock_ingredients' => $lowStockIngredients
        ]);
    }

    public function getStocks(Request $request)
    {
        try {
            ingredientBatch::processExpiredBatches();

            $ingredients = ingredients::with(['stockAlertLevel'])
                ->orderBy('name', 'asc')
                ->paginate(10);

            $ingredients->getCollection()->transform(function ($ingredient) {
                $alertLevel = $ingredient->stockAlertLevel;
                $lowStock = $alertLevel->low_stock ?? 50;
                $criticalStock = $alertLevel->critical_stock ?? 10;

                if ($ingredient->stocks <= $criticalStock) {
                    $ingredient->badge_class = 'bg-danger';
                    $ingredient->badge_text = 'Critical';
                } elseif ($ingredient->stocks <= $lowStock) {
                    $ingredient->badge_class = 'bg-warning';
                    $ingredient->badge_text = 'Low Stock';
                } else {
                    $ingredient->badge_class = 'bg-success';
                    $ingredient->badge_text = 'Good';
                }

                $ingredient->unit = strtolower($ingredient->unit) === 'pieces' ? 'pcs' : $ingredient->unit;

                return $ingredient;
            });

            return response()->json([
                'ingredients' => $ingredients
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ingredients: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStockBatches(Request $request)
    {
        try {
            // ✅ Process expired batches BEFORE loading batch list
            ingredientBatch::processExpiredBatches();

            $period = $request->get('period', 'thisweek');

            $startDate = $period === 'thisweek'
                ? now()->startOfWeek()
                : now()->subWeek()->startOfWeek();

            $endDate = $period === 'thisweek'
                ? now()->endOfWeek()
                : now()->subWeek()->endOfWeek();

            $batches = DB::table('ingredient_batches')
                ->join('ingredients', 'ingredient_batches.ingredient_id', '=', 'ingredients.id')
                ->select(
                    'ingredient_batches.id',
                    'ingredients.name as ingredient_name',
                    'ingredient_batches.quantity',
                    'ingredient_batches.expiration_date',
                    'ingredients.unit',
                    'ingredient_batches.arrived_at'
                )
                ->where('ingredient_batches.quantity', '>', 0)
                ->where('ingredient_batches.status', '!=', 'expired') // ✅ Exclude expired batches
                ->whereBetween('ingredient_batches.arrived_at', [$startDate, $endDate])
                ->whereDate('ingredient_batches.expiration_date', '>', now()) // This already filters out expired
                ->orderBy('ingredient_batches.arrived_at', 'desc')
                ->paginate(10)
                ->through(function ($b) {
                    return [
                        'id' => $b->id,
                        'ingredient_name' => $b->ingredient_name,
                        'quantity' => $b->quantity,
                        'unit' => $b->unit,
                        'arrived_at' => \Carbon\Carbon::parse($b->arrived_at)->format('Y-m-d'),
                        'expiration_date' => \Carbon\Carbon::parse($b->expiration_date)->format('Y-m-d')
                    ];
                });

            return response()->json(['batches' => $batches]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load stock batches: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getExpiredOnly(Request $request)
    {
        try {
            $expiredItems = DB::table('ingredient_batches')
                ->join('ingredients', 'ingredient_batches.ingredient_id', '=', 'ingredients.id')
                ->whereDate('ingredient_batches.expiration_date', '<=', now())
                ->where('ingredient_batches.quantity', '>', 0)
                ->select(
                    'ingredient_batches.id as batch_id',
                    'ingredients.name as ingredient_name',
                    'ingredient_batches.quantity',
                    'ingredients.unit',
                    'ingredient_batches.expiration_date'
                )
                ->orderBy('ingredient_batches.expiration_date', 'desc')
                ->paginate(10)
                ->through(function ($b) {
                    return [
                        'ingredient_name' => $b->ingredient_name,
                        'quantity' => $b->quantity,
                        'unit' => $b->unit,
                        'expiration_date' => $b->expiration_date,
                        'notes' => 'Batch expired'
                    ];
                });

            return response()->json(['expired_items' => $expiredItems]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load expired ingredients: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addingredient()
    {
        return view('admin.addingredient');
    }

    public function storeIngredient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:ingredients,name',
            'category' => 'required|in:meat,vegetables,soupbase,beverage',
            'unit' => 'required|in:kg,pieces'
        ]);

        try {
            $ingredientId = DB::table('ingredients')->insertGetId([
                'name' => $request->name,
                'category' => $request->category,
                'unit' => $request->unit,
                'stocks' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ingredient added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add ingredient: ' . $e->getMessage()
            ], 500);
        }
    }

    public function editIngredient($id)
    {
        $ingredient = DB::table('ingredients')->where('id', $id)->first();

        if (!$ingredient) {
            return redirect()->route('admin.ingredient_management')
                ->with('error', 'Ingredient not found');
        }

        return view('admin.editingredient', compact('ingredient'));
    }

    public function updateIngredient(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:ingredients,name,' . $id,
            'category' => 'required|in:meat,vegetables,soupbase,beverage',
            'unit' => 'required|in:kg,pieces'
        ]);

        DB::table('ingredients')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'category' => $request->category,
                'unit' => $request->unit,
                'updated_at' => now()
            ]);

        return redirect()->route('admin.ingredient_management')
            ->with('success', 'Ingredient updated successfully');
    }

    public function addStockForm()
    {
        $ingredients = ingredients::select('id', 'name', 'unit', 'stocks')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'ingredients' => $ingredients
        ]);
    }
    public function addStock(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
            'arrived_at' => 'required|date',
            'expiration_date' => 'required|date|after_or_equal:arrived_at'
        ]);

        DB::beginTransaction();
        try {
            $ingredient = ingredients::findOrFail($request->ingredient_id);

            ingredientBatch::create([
                'ingredient_id' => $request->ingredient_id,
                'quantity' => $request->quantity,
                'arrived_at' => $request->arrived_at,
                'expiration_date' => $request->expiration_date
            ]);

            $oldStock = $ingredient->stocks;
            $newStock = $oldStock + $request->quantity;
            $ingredient->update(['stocks' => $newStock]);

            ingredientMovements::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => Auth::id(),
                'type' => 'stock_in',
                'quantity' => $request->quantity,
                'stock_before' => $oldStock,
                'stock_after' => $newStock,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Stock added successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add Stock Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStockForm()
    {
        $ingredients = DB::table('ingredients')->get();
        return response()->json(['ingredients' => $ingredients]);
    }


    public function updateStock(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'new_quantity' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();


            $ingredient = DB::table('ingredients')->where('id', $request->ingredient_id)->first();
            $stockBefore = $ingredient->stocks;
            $stockAfter = $request->new_quantity;
            $difference = $stockAfter - $stockBefore;


            DB::table('ingredients')
                ->where('id', $request->ingredient_id)
                ->update(['stocks' => $stockAfter]);


            DB::table('ingredient_movements')->insert([
                'ingredient_id' => $request->ingredient_id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => $difference,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock: ' . $e->getMessage()
            ], 500);
        }
    }


    public function modifyStockForm()
    {
        $ingredients = DB::table('ingredients')
            ->where('stocks', '>', 0)
            ->get();
        return response()->json(['ingredients' => $ingredients]);
    }

    public function modifyStock(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity_used' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $ingredient = DB::table('ingredients')->where('id', $request->ingredient_id)->first();

            if ($ingredient->stocks < $request->quantity_used) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available. Current stock: ' . $ingredient->stocks
                ], 400);
            }

            $batches = DB::table('ingredient_batches')
                ->where('ingredient_id', $request->ingredient_id)
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date')
                ->orderBy('created_at')
                ->get();

            $remainingToUse = $request->quantity_used;

            foreach ($batches as $batch) {
                if ($remainingToUse <= 0) break;

                $useFromBatch = min($remainingToUse, $batch->quantity);

                DB::table('ingredient_batches')
                    ->where('id', $batch->id)
                    ->decrement('quantity', $useFromBatch);

                DB::table('ingredient_movements')->insert([
                    'ingredient_id' => $request->ingredient_id,
                    'user_id' => Auth::id(),
                    'ingredient_batch_id' => $batch->id,
                    'action' => 'Out',
                    'quantity' => $useFromBatch,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $remainingToUse -= $useFromBatch;
            }

            DB::table('ingredients')
                ->where('id', $request->ingredient_id)
                ->decrement('stocks', $request->quantity_used);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stock modified successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to modify stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateBatch(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'arrived_at' => 'required|date',
            'expiration_date' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $batch = ingredientBatch::findOrFail($id);
            $ingredient = $batch->ingredient;

            $oldQty = $batch->quantity;
            $newQty = $request->quantity;
            $diff = $newQty - $oldQty;

            $batch->update([
                'quantity' => $newQty,
                'arrived_at' => $request->arrived_at,
                'expiration_date' => $request->expiration_date
            ]);

            $ingredient->update(['stocks' => $ingredient->stocks + $diff]);

            ingredientMovements::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity' => $diff,
                'stock_before' => $ingredient->stocks - $diff,
                'stock_after' => $ingredient->stocks,
            ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false], 500);
        }
    }

    public function deleteBatch(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:ingredient_batches,id',
        ]);

        try {
            DB::beginTransaction();

            $batch = ingredientBatch::findOrFail($request->batch_id);
            $ingredient = $batch->ingredient;

            $ingredient->decrement('stocks', $batch->quantity);

            ingredientMovements::create([
                'ingredient_id' => $batch->ingredient_id,
                'ingredient_batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'type' => 'stock_out',
                'quantity' => $batch->quantity,
                'stock_before' => $ingredient->stocks + $batch->quantity,
                'stock_after' => $ingredient->stocks,
            ]);

            $batch->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Batch deleted and stock updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete batch: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getLowStockAlerts()
    {
        try {
            $lowStock = ingredients::whereColumn('stocks', '<=', 'low_stock')
                ->orderBy('category')
                ->orderBy('name')
                ->get();

            return response()->json([
                'low_stock_ingredients' => $lowStock
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load low stock alerts: ' . $e->getMessage()
            ], 500);
        }
    }
    public function printStockRequest($id)
    {
        $ingredient = ingredients::with(['stockAlertLevel', 'stockOrders'])->findOrFail($id);

        // Get the pending order for this ingredient
        $order = $ingredient->stockOrders()
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$order) {
            return back()->with('error', 'No pending order found for this ingredient.');
        }

        // Determine stock status
        $stockStatus = 'normal';
        if ($ingredient->stockAlertLevel) {
            if ($ingredient->stocks <= $ingredient->stockAlertLevel->critical_stock) {
                $stockStatus = 'critical';
            } elseif ($ingredient->stocks <= $ingredient->stockAlertLevel->low_stock) {
                $stockStatus = 'low';
            }
        }

        $data = [
            'date' => now()->format('M d, Y'),
            'requestedBy' => Auth::user()->name,
            'ingredient' => [
                'name' => $ingredient->name,
                'stocks' => $ingredient->stocks,
                'unit' => $ingredient->unit,
                'stock_status' => $stockStatus,
            ],
            'alertLevel' => [
                'low_stock' => $ingredient->stockAlertLevel->low_stock ?? 0,
                'critical_stock' => $ingredient->stockAlertLevel->critical_stock ?? 0,
                'reorder_quantity' => $ingredient->stockAlertLevel->reorder_quantity ?? 0,
            ],
            'order' => [
                'id' => $order->id,
                'quantity' => $order->quantity,
                'status' => ucfirst($order->status),
                'notes' => $order->notes ?? null,
                'created_at' => $order->created_at->format('M d, Y h:i A'),
            ],
        ];

        $pdf = Pdf::loadView('admin.pdf.stock_request', $data);
        return $pdf->stream("purchase_order_{$order->id}_{$ingredient->name}.pdf");
    }


    public function others(Request $request)
    {
        $hours = OperatingHour::all();
        $menus = menu::whereNull('deleted_at')->get();
        $discounts = MenuDiscount::with('menu')->paginate(6);
        $stock_level = StockAlertLevel::with('ingredient')->paginate(6);
        $stock_order = StockOrder::with('ingredient')->paginate(6);
        $ingredients = ingredients::orderBy('name')->get();

        $ingredientsWithAlerts = StockAlertLevel::pluck('ingredient_id');
        $ingredients_without_alerts = ingredients::whereNotIn('id', $ingredientsWithAlerts)
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            $section = $request->get('section');

            if ($section === 'discounts') {
                return view('admin.others', compact('hours', 'menus', 'discounts', 'stock_level', 'stock_order', 'ingredients', 'ingredients_without_alerts'))->render();
            }

            if ($section === 'stock') {
                return view('admin.others', compact('hours', 'menus', 'discounts', 'stock_level', 'stock_order', 'ingredients', 'ingredients_without_alerts'))->render();
            }

            if ($section === 'stock_order') {
                return view('admin.others', compact('hours', 'menus', 'discounts', 'stock_level', 'stock_order', 'ingredients', 'ingredients_without_alerts'))->render();
            }
        }

        return view('admin.others', compact('hours', 'menus', 'discounts', 'stock_level', 'stock_order', 'ingredients', 'ingredients_without_alerts'));
    }


    public function storeOperatingHours(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'open_time' => 'required_without:is_closed|date_format:H:i',
            'close_time' => 'required_without:is_closed|date_format:H:i',
            'is_closed' => 'nullable|boolean'
        ]);

        $existing = OperatingHour::where('date', $request->date)
            ->where('is_default', false)
            ->first();

        if ($existing) {
            $existing->update([
                'open_time' => $request->has('is_closed') ? null : $request->open_time,
                'close_time' => $request->has('is_closed') ? null : $request->close_time,
                'is_closed' => $request->has('is_closed')
            ]);
        } else {
            OperatingHour::create([
                'is_default' => false,
                'date' => $request->date,
                'open_time' => $request->has('is_closed') ? null : $request->open_time,
                'close_time' => $request->has('is_closed') ? null : $request->close_time,
                'is_closed' => $request->has('is_closed')
            ]);
        }

        return redirect()->back()->with('success', 'Date-specific hours set successfully!');
    }

    public function updateOperatingHours(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'open_time' => 'required_without:is_closed|date_format:H:i',
            'close_time' => 'required_without:is_closed|date_format:H:i',
            'is_closed' => 'nullable|boolean'
        ]);

        $hours = OperatingHour::findOrFail($id);

        $hours->update([
            'date' => $request->date,
            'open_time' => $request->has('is_closed') ? null : $request->open_time,
            'close_time' => $request->has('is_closed') ? null : $request->close_time,
            'is_closed' => $request->has('is_closed')
        ]);

        return redirect()->back()->with('success', 'Operating hours updated successfully!');
    }

    public function deleteOperatingHours($id)
    {
        $hours = OperatingHour::findOrFail($id);

        if ($hours->is_default) {
            return redirect()->back()->with('error', 'Cannot delete default operating hours!');
        }

        $hours->delete();

        return redirect()->back()->with('success', 'Date override removed successfully!');
    }
    public function storeDiscount(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menu,id',
            'discount_type' => 'required|in:Student,Government Employee,Senior Citizen,PWD',
            'discount_percentage' => 'required|numeric|min:0|max:100'
        ]);

        MenuDiscount::create($request->all());

        return redirect()->back()->with('success', 'Discount added successfully!');
    }

    public function updateDiscount(Request $request, $id)
    {
        $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100'
        ]);

        $discount = MenuDiscount::findOrFail($id);
        $discount->update(['discount_percentage' => $request->discount_percentage]);

        return redirect()->back()->with('success', 'Discount updated successfully!');
    }

    // Store new stock alert
    public function storeStockAlert(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id|unique:stock_level_alerts,ingredient_id',
            'low_stock' => 'required|numeric|min:0',
            'critical_stock' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0.01',
        ]);

        StockAlertLevel::create([
            'ingredient_id' => $request->ingredient_id,
            'low_stock' => $request->low_stock,
            'critical_stock' => $request->critical_stock,
            'reorder_quantity' => $request->reorder_quantity,
        ]);

        return response()->json(['success' => true, 'message' => 'Stock alert created successfully']);
    }

    // Update stock alert
    public function updateStockAlert(Request $request, $id)
    {
        $request->validate([
            'low_stock' => 'required|numeric|min:0',
            'critical_stock' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0.01',
        ]);

        $stockAlert = StockAlertLevel::findOrFail($id);

        $stockAlert->update([
            'low_stock' => $request->low_stock,
            'critical_stock' => $request->critical_stock,
            'reorder_quantity' => $request->reorder_quantity,
        ]);

        return response()->json(['success' => true, 'message' => 'Stock alert updated successfully']);
    }

    // Delete stock alert
    public function deleteStockAlert($id)
    {
        $stockAlert = StockAlertLevel::findOrFail($id);
        $stockAlert->delete();

        return response()->json(['success' => true, 'message' => 'Stock alert deleted successfully']);
    }


    public function ewallet_management()
    {
        $ewallet_details = DB::table('ewallet_details')->get();

        $receipts = DB::table('reservation_payment_details')
            ->leftJoin('reservations', 'reservation_payment_details.reservation_id', '=', 'reservations.id')
            ->leftJoin('customers', 'reservations.customer_id', '=', 'customers.id')
            ->whereNotNull('reservation_payment_details.payment_proof')
            ->whereIn('reservation_payment_details.payment_method', ['gcash', 'maya'])
            ->select(
                'reservation_payment_details.id',
                'reservation_payment_details.payment_proof',
                'reservation_payment_details.created_at as submitted_at',
                DB::raw('COALESCE(customers.name, "Unknown Customer") as customer_name')
            )
            ->orderBy('reservation_payment_details.created_at', 'desc')
            ->get();

        return view('admin.ewallet_management', compact('ewallet_details', 'receipts'));
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

    public function feedback(Request $request)
    {
        $query = Feedback::query();

        if ($request->filled('search')) {
            $query->where('message', 'like', '%' . $request->search . '%');
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            $html = '';

            foreach ($feedbacks as $fb) {
                $html .= "<div class='mb-3 p-3 border rounded shadow-sm bg-white'>";
                $html .= "<p><strong>Message:</strong> {$fb->message}</p>";
                $html .= "<p class='text-muted'><strong>Submitted At:</strong> {$fb->created_at->format('d M Y, h:i A')}</p>";
                $html .= "</div>";
            }

            if ($feedbacks->hasPages()) {
                $html .= "<div class='mt-3 d-flex justify-content-center'>";
                $html .= $feedbacks->links('pagination::bootstrap-4');
                $html .= "</div>";
            }

            return response()->json(['feedbacks' => $html]);
        }

        return view('admin.feedback', compact('feedbacks'));
    }
}
