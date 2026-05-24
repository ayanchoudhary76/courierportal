<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        // Already logged in as admin → redirect to dashboard
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Find user without logging in so we can check 2FA first
        $user = \App\Models\User::where('email', $request->email)
            ->where('role', 'admin')
            ->where('is_active', true)
            ->first();

        if (! $user || ! \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Invalid credentials or account inactive.',
            ])->onlyInput('email');
        }

        // If 2FA is enabled, store user ID in session and go to OTP challenge
        if ($user->google2fa_enabled) {
            session(['2fa_user_id' => $user->id]);
            return redirect()->route('admin.2fa.challenge');
        }

        // No 2FA — login directly
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Log the admin out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Logged out successfully.');
    }
}
