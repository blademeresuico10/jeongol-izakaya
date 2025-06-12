<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {

        return view('login'); 
    }


    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'user_id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
            ]);

            // Check role and redirect accordingly
            if ($user->role === 'admin') {
                return redirect()->route('admin.home');
            } else {
                
                return redirect()->route('dashboard'); // fallback
            }
        } else {
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
