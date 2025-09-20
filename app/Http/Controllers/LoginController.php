<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;


class LoginController extends Controller
{
    public function index()
    {
        return view('staff_login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $user = User::where('username', $request->username)
            ->where('status', '!=', 'Deleted')
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role === 'Admin') {
                RateLimiter::hit($key, 60);
                return back()->withErrors(['login' => 'Admin users cannot login here. Please use the admin login page.'])->withInput();
            }

            if ($user->status === 'Inactive') {
                return back()->withErrors(['login' => 'Your account has been deactivated. Please contact support for assistance.'])->withInput();
            }

            if ($user->status === 'Deleted') {
                return back()->withErrors(['login' => 'This account no longer exists. Please contact support if you believe this is an error.'])->withInput();
            }

            // Check if another user with same role is already logged in
            if (User::isRoleActive($user->role, $user->id)) {
                return back()->withErrors([
                    'login' => "Another {$user->role} is already logged in. Only one {$user->role} can be active at a time."
                ])->withInput();
            }

            // Generate session token and mark user as logged in
            $sessionToken = Str::random(60);
            $user->markAsLoggedIn($sessionToken);

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            $request->session()->put('user_session_token', $sessionToken);

            RateLimiter::clear($key);

            return $this->redirectBasedOnRole($user);
        } else {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }
    }

    public function adminLogin()
    {
        return view('admin_login');
    }

    public function adminLoginSubmit(Request $request)
    {
        try {
            Log::info('Admin login attempt started', ['username' => $request->username]);

            $this->validateLogin($request);
            Log::info('Validation passed');

            $key = $this->throttleKey($request, 'admin');

            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                throw ValidationException::withMessages([
                    'username' => "Too many admin login attempts. Please try again in {$seconds} seconds.",
                ]);
            }

            $user = User::where('username', $request->username)
                ->where('status', '!=', 'Deleted')
                ->first();

            Log::info('User query executed', ['user_found' => $user ? 'yes' : 'no']);

            if ($user && Hash::check($request->password, $user->password)) {
                Log::info('Password check passed', ['user_role' => $user->role]);

                if ($user->role !== 'Admin') {
                    RateLimiter::hit($key, 300);
                    return back()->withErrors([
                        'username' => 'Only Admin users can login here. Please use the staff login page.',
                    ])->withInput($request->only('username'));
                }

                if ($user->status === 'Inactive') {
                    return back()->withErrors(['username' => 'Your admin account has been deactivated. Please contact support for assistance.']);
                }

                if ($user->status === 'Deleted') {
                    return back()->withErrors(['username' => 'This admin account no longer exists. Please contact support if you believe this is an error.']);
                }

                // Check if another admin is already logged in
                if (User::isRoleActive('Admin', $user->id)) {
                    return back()->withErrors([
                        'username' => 'Another Admin is already logged in. Only one Admin can be active at a time.'
                    ])->withInput($request->only('username'));
                }

                // Generate session token and mark user as logged in
                $sessionToken = Str::random(60);
                $user->markAsLoggedIn($sessionToken);

                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                $request->session()->put('user_session_token', $sessionToken);
                
                RateLimiter::clear($key);

                return redirect()->route('admin.dashboard');
            } else {
                Log::info('Login failed - invalid credentials');
                RateLimiter::hit($key, 300);
                return back()->withErrors([
                    'username' => 'The provided credentials do not match our records.',
                ])->withInput($request->only('username'));
            }
        } catch (\Exception $e) {
            Log::error('Admin login error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['username' => 'Login failed. Please try again.']);
        }
    }

    public function showAdminForgotPasswordForm()
    {
        return view('admin_forgot_password');
    }

    public function sendAdminResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Check if admin exists
        $admin = User::where('email', $request->email)
            ->where('role', 'Admin')
            ->where('status', 'Active')
            ->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'This email is not associated with an active admin account.'
            ]);
        }

        // Generate simple 6-digit code
        $code = rand(100000, 999999);

        // Store code in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($code),
                'created_at' => now()
            ]
        );

        try {
            Mail::send('emails.admin_password_reset', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Admin Password Reset Code - Jeongol Restaurant');
            });

            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please try again.'
            ]);
        }
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No reset request found.'
            ]);
        }

        if (now()->diffInMinutes($resetRecord->created_at) > 15) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Code has expired. Please request a new one.'
            ]);
        }

        // Verify the code
        if (!Hash::check($request->code, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code. Please try again.'
            ]);
        }

        $resetToken = Str::random(60);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'token' => Hash::make($resetToken),
                'created_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Code verified! Set your new password.',
            'reset_token' => $resetToken
        ]);
    }

    public function showAdminResetForm(Request $request, $token)
    {
        return view('admin_reset_password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetAdminPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid reset token.'
                ]);
            }
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Find admin user
        $admin = User::where('email', $request->email)
            ->where('role', 'Admin')
            ->where('status', 'Active')
            ->first();

        if (!$admin) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin account not found.'
                ]);
            }
            return back()->withErrors(['email' => 'Admin account not found.']);
        }

        // Update password
        $admin->update(['password' => Hash::make($request->password)]);

        // Clean up
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully!'
            ]);
        }

        return redirect()->route('admin.login')
            ->with('status', 'Password reset successfully!');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role ?? null;

        // Mark user as logged out in database
        if ($user) {
            $user->markAsLoggedOut();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userRole === 'Admin') {
            return redirect()->route('admin.login');
        } else {
            return redirect()->route('login'); 
        }
    }

    /**
     * Force logout endpoint for AJAX/API calls
     */
    public function forceLogout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            $user->markAsLoggedOut();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('login');
    }

    /**
     * Session validation endpoint for AJAX calls
     */
    public function validateSession(Request $request)
    {
        $user = Auth::user();
        $sessionToken = $request->session()->get('user_session_token');

        if (!$user || !$sessionToken || !$user->isSessionValid($sessionToken)) {
            return response()->json(['valid' => false], 401);
        }

        $activeUser = User::getActiveUserByRole($user->role);
        if (!$activeUser || $activeUser->id !== $user->id) {
            $user->markAsLoggedOut();
            Auth::logout();
            return response()->json(['valid' => false], 401);
        }

        return response()->json(['valid' => true]);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
    }

    protected function throttleKey(Request $request, $prefix = 'login')
    {
        return strtolower($prefix . '|' . $request->input('username') . '|' . $request->ip());
    }

    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'Admin':
                return redirect()->route('admin.home');
            case 'Receptionist':
                return redirect()->route('receptionist.home');
            case 'Kitchen Staff':
                return redirect()->route('kitchen.home');
            case 'Cashier':
                return redirect()->route('cashier.home');
            default:
                return redirect()->route('login')->withErrors(['error' => 'Invalid user role.']);
        }
    }
}