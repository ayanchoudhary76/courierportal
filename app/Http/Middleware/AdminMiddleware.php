<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Not authenticated at all → login page
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Authenticated but not an admin role → hard 403
        if ($user->role !== 'admin') {
            abort(403, 'Access denied. Admin only.');
        }

        // Admin but account suspended → force logout
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your account has been suspended.']);
        }

        return $next($request);
    }
}
