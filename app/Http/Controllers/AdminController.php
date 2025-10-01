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
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Log;
use App\Models\expiredIngredients;
use App\Models\ingredientBatch;
use App\Models\ingredientMovements;


class AdminController extends Controller
{
    public function home()
    {
        $todayRevenue = DB::table('transactions')
            ->whereDate('created_at', Carbon::today())
            ->where('status', '!=', 'Refunded')
            ->sum('grand_total');  

        $todayCustomers = DB::table('reservations')
            ->whereDate('reservation_time', Carbon::today())
            ->whereIn('status', ['Accepted', 'Completed'])
            ->sum('pax');

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
                    'grand_total' => $transaction->grand_total, 
                    'orders_total' => $transaction->orders_total, 
                    'discount_total' => $transaction->discount_total, 
                    'advance_payment' => $transaction->advance_payment ?? 0,
                    'cashier' => (object)[
                        'firstname' => $transaction->firstname,
                        'lastname' => $transaction->lastname,
                    ]
                ];
            });

        $popularMenusToday = DB::table('menu')
            ->join('order_details', 'menu.id', '=', 'order_details.menu_id')
            ->select(
                'menu.id',
                'menu.menu_item',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->where('order_details.status', 'Served')
            ->whereDate('order_details.created_at', Carbon::today())
            ->where('menu.status', 'Active')
            ->where('menu.category', 'Main')
            ->whereNull('menu.deleted_at')
            ->groupBy('menu.id', 'menu.menu_item')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $popularMenusWeek = DB::table('menu')
            ->join('order_details', 'menu.id', '=', 'order_details.menu_id')
            ->select(
                'menu.id',
                'menu.menu_item',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->where('order_details.status', 'Served')
            ->whereBetween('order_details.created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->where('menu.status', 'Active')
            ->where('menu.category', 'Main')
            ->whereNull('menu.deleted_at')
            ->groupBy('menu.id', 'menu.menu_item')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $popularMenusMonth = DB::table('menu')
            ->join('order_details', 'menu.id', '=', 'order_details.menu_id')
            ->select(
                'menu.id',
                'menu.menu_item',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
            ->where('order_details.status', 'Served')
            ->whereBetween('order_details.created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->where('menu.status', 'Active')
            ->where('menu.category', 'Main')
            ->whereNull('menu.deleted_at')
            ->groupBy('menu.id', 'menu.menu_item')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $monthlyTransactions = DB::table('transactions')
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', '!=', 'Refunded')
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('M'));

        return view('admin.home', compact(
            'todayRevenue',
            'todayCustomers',
            'transactions',
            'popularMenusToday',
            'popularMenusWeek',
            'popularMenusMonth',
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

    public function menuIngredients()
    {
        $menus = DB::table('menu')
            ->where('category', 'main')
            ->whereNull('deleted_at')
            ->get();

        $ingredients = DB::table('menu_ingredients')
            ->join('ingredients', 'menu_ingredients.ingredient_id', '=', 'ingredients.id')
            ->select(
                'menu_ingredients.id',
                'menu_ingredients.menu_id',
                'menu_ingredients.quantity',
                'ingredients.name as ingredient_name'
            )
            ->get()
            ->groupBy('menu_id');

        return response()->json([
            'menus' => $menus,
            'ingredients' => $ingredients,
        ]);
    }

    public function getIngredients($id)
    {
        $menu = Menu::findOrFail($id);

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

        // Check if ingredient already exists for this menu
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

            // Check if there are any batches at all
            $allBatches = DB::table('ingredient_batches')->count();

            // Check date range
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

    public function getExpiredOnly()
    {
        try {

            $expiredItems = DB::table('expired_ingredients')
                ->select(
                    'id',
                    'ingredient_name',
                    'quantity',
                    'expiration_date',
                    'expired_at',
                    'unit',
                    'category',
                    'notes',
                    DB::raw("DATEDIFF(CURDATE(), expired_at) as days_since_marked")
                )
                ->orderBy('expired_at', 'desc')
                ->get();

            return response()->json(['expired_items' => $expiredItems]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load expired items: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeMenu(Request $request)
    {
        try {
            $request->validate(
                [
                    'menu_item' => 'required|string|max:255|unique:menu,menu_item,NULL,id,deleted_at,NULL',
                    'category' => 'required|in:main,add_ons',
                    'regular_price' => 'required|numeric|min:0',
                    'student_price' => 'nullable|numeric|min:0',
                    'govt_employee_price' => 'nullable|numeric|min:0',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                ],
                [
                    'menu_item.required' => 'Menu item name is required.',
                    'menu_item.unique' => 'The menu is existing.',
                    'menu_item.max' => 'Menu item name cannot exceed 255 characters.',
                    'category.required' => 'Category is required.',
                    'category.in' => 'Category must be either Main or Add-ons.',
                    'regular_price.required' => 'Regular price is required.',
                    'regular_price.numeric' => 'Regular price must be a valid number.',
                    'regular_price.min' => 'Regular price cannot be negative.',
                    'student_price.numeric' => 'Student price must be a valid number.',
                    'student_price.min' => 'Student price cannot be negative.',
                    'govt_employee_price.numeric' => 'Government employee price must be a valid number.',
                    'govt_employee_price.min' => 'Government employee price cannot be negative.',
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

            DB::table('menu')->insert([
                'menu_item' => $request->menu_item,
                'category' => $request->category,
                'regular_price' => $request->regular_price,
                'student_price' => $request->student_price,
                'govt_employee_price' => $request->govt_employee_price,
                'has_customer_discount' => false,
                'status' => 'Active',
                'image' => $imageName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('admin.menu_management')
                ->with('success', 'Menu item added successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator);
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
                'has_customer_discount' => 'nullable|boolean',
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

            $menu = menu::findOrFail($id);

            if ($request->status === 'Blocked' && $menu->status !== 'Blocked') {
                $hasActiveOrders = OrderDetail::where('menu_id', $id)
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
                'student_price' => $request->student_price,
                'govt_employee_price' => $request->govt_employee_price,
                'has_customer_discount' => (bool) $request->has_customer_discount,
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

        $nextTableNumber = $this->getNextTableNumber();

        return view('admin.table_management', compact('tables', 'nextTableNumber'));
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
        $ingredients = DB::table('ingredients')->get();
        return view('admin.ingredient_management', compact('ingredients'));
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
            'unit' => 'required|in:grams,pieces'
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
            'unit' => 'required|in:grams,pieces'
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
        $ingredients = DB::table('ingredients')->get();
        return response()->json(['ingredients' => $ingredients]);
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0.01',
            'expiration_date' => 'required|date|after_or_equal:today'
        ]);

        try {
            DB::beginTransaction();

            // Get ingredient details
            $ingredient = DB::table('ingredients')->where('id', $request->ingredient_id)->first();
            $stockBefore = $ingredient->stocks;
            $stockAfter = $stockBefore + $request->quantity;

            // Add to ingredient_batches
            DB::table('ingredient_batches')->insert([
                'ingredient_id' => $request->ingredient_id,
                'quantity' => $request->quantity,
                'expiration_date' => $request->expiration_date,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update total stock
            DB::table('ingredients')
                ->where('id', $request->ingredient_id)
                ->update(['stocks' => $stockAfter]);

            DB::table('ingredient_movements')->insert([
                'ingredient_id' => $request->ingredient_id,
                'user_id' => Auth::id(),
                'type' => 'stock_in',
                'quantity' => $request->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'notes' => 'Stock added via Add Stock form',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add stock: ' . $e->getMessage()
            ], 500);
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

            // Get current stock
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
                'notes' => 'Manual stock adjustment',
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

    public function getStockBatches(Request $request)
    {
        try {
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
                    'ingredient_batches.created_at'
                )
                ->where('ingredient_batches.quantity', '>', 0)
                ->whereBetween('ingredient_batches.created_at', [$startDate, $endDate])
                ->orderBy('ingredient_batches.created_at', 'desc')
                ->get();

            return response()->json(['batches' => $batches]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load stock batches: ' . $e->getMessage()
            ], 500);
        }
    }

    public function analytics()
    {
        $currentYear = now()->year;

        $monthlyData = DB::table('transactions')
            ->whereNotNull('created_at')
            ->where('status', '!=', 'Refunded')
            ->whereYear('created_at', $currentYear)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(orders_total) as gross_revenue'),       
                DB::raw('SUM(grand_total) as net_revenue'),           
                DB::raw('SUM(discount_total) as total_discounts')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy('month');

        $labels = [];
        $grossRevenue = [];
        $netRevenue = [];
        $discounts = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = \Carbon\Carbon::create($currentYear, $month, 1)->format('M');

            if (isset($monthlyData[$month])) {
                $grossRevenue[] = (float) $monthlyData[$month]->gross_revenue;
                $netRevenue[] = (float) $monthlyData[$month]->net_revenue;
                $discounts[] = (float) $monthlyData[$month]->total_discounts;
            } else {
                $grossRevenue[] = 0;
                $netRevenue[] = 0;
                $discounts[] = 0;
            }
        }

        $yearStats = DB::table('transactions')
            ->where('status', '!=', 'Refunded')
            ->whereYear('created_at', $currentYear)
            ->selectRaw('
            COUNT(*) as count,
            SUM(grand_total) as revenue,
            SUM(orders_total) as gross_revenue,
            SUM(discount_total) as discounts
        ')
            ->first();

        return view('admin.analytics', compact(
            'labels',
            'grossRevenue',
            'netRevenue',
            'discounts',
            'yearStats',
            'currentYear'
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
            ->sum('grand_total') ?? 0;

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
