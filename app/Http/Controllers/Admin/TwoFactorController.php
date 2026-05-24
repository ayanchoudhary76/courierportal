<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    // ── Show QR code setup page (first-time only) ─────────────────────
    public function setup()
    {
        $user = Auth::user();

        if ($user->google2fa_enabled) {
            return redirect()->route('admin.dashboard')
                ->with('info', '2FA is already enabled on your account.');
        }

        // Generate a new secret if not already generated
        if (! $user->google2fa_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update(['google2fa_secret' => $secret]);
            $user->refresh();
        }

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        // Generate QR code as SVG string locally (no external HTTP request)
        $qrCodeSvg = QrCode::size(200)
            ->format('svg')
            ->errorCorrection('M')
            ->generate($qrCodeUrl);

        return view('admin.auth.2fa-setup', [
            'qrCodeSvg' => $qrCodeSvg,
        ]);
    }

    // ── Confirm and enable 2FA after scanning QR ──────────────────────
    public function enable(Request $request)
    {
        $request->validate([
            'one_time_password' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();

        $valid = $this->google2fa->verifyKey(
            $user->google2fa_secret,
            $request->one_time_password
        );

        if (! $valid) {
            return back()->withErrors(['one_time_password' => 'Invalid code. Please try again.']);
        }

        $user->update(['google2fa_enabled' => true]);

        return redirect()->route('admin.dashboard')
            ->with('success', '2FA enabled successfully! Your account is now more secure.');
    }

    // ── Show OTP verification page (after password login) ─────────────
    public function challenge()
    {
        if (! session()->has('2fa_user_id')) {
            return redirect()->route('admin.login');
        }

        return view('admin.auth.2fa-challenge');
    }

    // ── Verify OTP during login ────────────────────────────────────────
    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => ['required', 'digits:6'],
        ]);

        $userId = session('2fa_user_id');

        if (! $userId) {
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $user = User::findOrFail($userId);

        $valid = $this->google2fa->verifyKey(
            $user->google2fa_secret,
            $request->one_time_password
        );

        if (! $valid) {
            return back()->withErrors(['one_time_password' => 'Invalid OTP. Please try again.']);
        }

        // Complete the login
        Auth::login($user);
        session()->forget('2fa_user_id');
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    // ── Disable 2FA ───────────────────────────────────────────────────
    public function disable(Request $request)
    {
        $request->validate([
            'one_time_password' => ['required', 'digits:6'],
            'password'          => ['required'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect account password.']);
        }

        $valid = $this->google2fa->verifyKey(
            $user->google2fa_secret,
            $request->one_time_password
        );

        if (! $valid) {
            return back()->withErrors(['one_time_password' => 'Invalid OTP code.']);
        }

        $user->update([
            'google2fa_enabled' => false,
            'google2fa_secret'  => null,
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', '2FA has been disabled on your account.');
    }
}
