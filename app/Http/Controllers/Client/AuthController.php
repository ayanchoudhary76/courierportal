<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\RateCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ─── Login ────────────────────────────────────────────────────────
    public function showLogin()
    {
        return view('client.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt with role + active constraints baked in
        $user = User::where('email', $credentials['email'])
            ->where('role', 'client')
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials or account inactive.'])->withInput();
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'Your account has been suspended. Please contact support.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('client.dashboard'));
    }

    // ─── Register ─────────────────────────────────────────────────────
    public function showRegister()
    {
        return view('client.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'phone'        => ['required', 'string', 'max:15'],
            'password'     => ['required', 'confirmed', Password::min(8)],
            'company_name' => ['required', 'string', 'max:150'],
            'gstin'        => ['nullable', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'address'      => ['required', 'string'],
            'city'         => ['required', 'string', 'max:60'],
            'pincode'      => ['required', 'digits:6'],
            'state'        => ['required', 'string', 'max:60'],
            'terms'        => ['required', 'accepted'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'],
                'password'  => Hash::make($validated['password']),
                'role'      => User::ROLE_CLIENT,
                'is_active' => true,
            ]);

            $defaultCard = RateCard::where('is_default', true)->first();

            Client::create([
                'user_id'      => $user->id,
                'company_name' => $validated['company_name'],
                'gstin'        => $validated['gstin'] ?? null,
                'address'      => $validated['address'],
                'city'         => $validated['city'],
                'pincode'      => $validated['pincode'],
                'state'        => $validated['state'],
                'account_type' => 'prepaid',
                'credit_limit' => 0,
                'rate_card_id' => $defaultCard?->id,
                'is_active'    => true,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('client.dashboard')
            ->with('success', 'Welcome to CourierPortal! Your account is ready.');
    }

    // ─── Forgot Password ──────────────────────────────────────────────
    public function showForgotPassword()
    {
        return view('client.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Full password reset implementation in a later phase
        return back()->with('status', 'If this email is registered, a password reset link has been sent.');
    }

    // ─── Logout ───────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
