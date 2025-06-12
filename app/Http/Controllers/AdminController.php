<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.home');
    }

    public function users()
    {
        $users = User::all(); // Fetch all users from the database
        return view('admin.users', compact('users')); // Pass the users to the Blade view
    }

    public function adduser()
    {
        return view('admin.adduser');
    }

    public function storeUser(Request $request)
    {
        // Validate input
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'role' => 'required|string',
            'contact' => 'required|string|max:20',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Create new user
        User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'role' => $request->role,
            'contact_number' => $request->contact,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Redirect back to user list
        return redirect()->route('admin.users')->with('success', 'User added successfully!');
    }

    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string',
        ]);

        // Find the user
        $user = User::findOrFail($id);

        // Update fields
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
            'created_at' => now(),
            'updated_at' => now(),
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
        $tables = DB::table('tables')->get(); // Fetch all tables from the database
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




    public function reports()
    {
        return view('admin.reports');
    }
}
