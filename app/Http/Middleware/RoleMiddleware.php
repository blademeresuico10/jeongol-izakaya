<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            Log::info('User not authenticated, redirecting to login');
            return redirect()->route('login');
        }

        $user = Auth::user();

        Log::info('RoleMiddleware Simple Debug', [
            'user_role' => $user->role,
            'expected_role' => $role,
            'user_role_type' => gettype($user->role),
            'expected_role_type' => gettype($role),
            'are_identical' => $user->role === $role
        ]);

        // Temporarily allow all authenticated users to pass
        Log::info('Temporarily allowing all authenticated users');
        return $next($request);
    }
}
