<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\OperatingHour;

class CheckOperatingHours
{
    public function handle(Request $request, Closure $next)
    {
        $operating = OperatingHour::whereDate('date', today())->first() 
                     ?? OperatingHour::where('is_default', true)->first();

        if (!$operating) {
            return $next($request);
        }

        if ($operating->is_closed) {
            return $this->blocked($request);
        }

        if (!$operating->open_time || !$operating->close_time) {
            return $next($request);
        }

        $now = now()->format('H:i:s');
        $open = $operating->open_time;
        $close = $operating->close_time;

        if ($open < $close) {
            if ($now >= $open && $now < $close) {
                return $next($request);
            }
        } 
        else {
            if ($now >= $open || $now < $close) {
                return $next($request);
            }
        }

        return $this->blocked($request);
    }

    protected function blocked($request)
    {
        if (auth()->check()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('operation_closed');
    }
}