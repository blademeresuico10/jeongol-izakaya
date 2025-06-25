<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            
        Auth::login($user); // Laravel will now manage the session
        $request->session()->regenerate(); // Security best practice

            // Check role and redirect accordingly
            if ($user->role === 'admin') {
                return redirect()->route('admin.home');
            } 

            else if ($user->role === 'receptionist') {
                
                return redirect()->route('receptionist.home'); 
            }

            else if ($user->role === 'kitchen') {
                return redirect()->route('kitchen.home');
            }

            else{
                return redirect()->route('/');
            }


        } else {
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
