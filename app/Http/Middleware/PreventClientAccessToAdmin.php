<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventClientAccessToAdmin
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && Auth::user()->role !== 'admin') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('client.login')
                ->with('error', 'Access denied. Please log in with an admin account.');
        }

        return $next($request);
    }
}
