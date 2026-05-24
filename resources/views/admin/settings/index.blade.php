@extends('admin.layouts.app')

@section('page-title')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6">
        <circle cx="12" cy="12" r="3"/>
        <path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
    </svg>
    Settings
@endsection

@push('styles')
<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }
    @media(max-width: 900px) { .settings-grid { grid-template-columns: 1fr; } }

    .settings-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .settings-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .settings-card-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 18px;
    }
    .form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="tel"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 10px 13px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13.5px;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-sizing: border-box;
    }
    .form-group input:focus {
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }
    .form-group .input-wrap {
        position: relative;
    }
    .form-group .input-wrap input {
        padding-right: 40px;
    }
    .form-group .toggle-pw {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        padding: 0;
        line-height: 1;
    }
    .form-group .toggle-pw:hover { color: #475569; }

    .error-msg {
        font-size: 12px;
        color: #dc2626;
        margin-top: 4px;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 22px;
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
    }
    .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 12px 16px;
        color: #15803d;
        font-size: 13.5px;
        font-weight: 500;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>
@endpush

@section('content')

{{-- Flash success --}}
@if(session('success'))
<div class="alert-success">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    {{ session('success') }}
</div>
@endif

<div class="settings-grid">

    {{-- ── Card 1: Update Profile ─────────────────────────────────── --}}
    <div class="settings-card">
        <div class="settings-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2">
                <circle cx="12" cy="8" r="4"/>
                <path d="M20 21a8 8 0 1 0-16 0"/>
            </svg>
            Update Profile
        </div>
        <div class="settings-card-body">
            <form method="POST" action="{{ route('admin.settings.profile') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $admin->name) }}"
                           placeholder="Your full name">
                    @error('name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $admin->email) }}"
                           placeholder="admin@example.com">
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone', $admin->phone) }}"
                           placeholder="+91 9876543210">
                    @error('phone')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save Profile
                </button>
            </form>
        </div>
    </div>

    {{-- ── Card 2: Change Password ─────────────────────────────────── --}}
    <div class="settings-card" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
        <div class="settings-card-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            Change Password
        </div>
        <div class="settings-card-body">
            <form method="POST" action="{{ route('admin.settings.password') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="input-wrap">
                        <input :type="showCurrent ? 'text' : 'password'"
                               id="current_password" name="current_password"
                               placeholder="Enter current password">
                        <button type="button" class="toggle-pw" @click="showCurrent = !showCurrent">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <template x-if="!showCurrent">
                                    <g><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></g>
                                </template>
                                <template x-if="showCurrent">
                                    <g><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></g>
                                </template>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">New Password <span style="color:#94a3b8;font-weight:400;">(min 8 chars)</span></label>
                    <div class="input-wrap">
                        <input :type="showNew ? 'text' : 'password'"
                               id="password" name="password"
                               placeholder="Enter new password">
                        <button type="button" class="toggle-pw" @click="showNew = !showNew">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <template x-if="!showNew">
                                    <g><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></g>
                                </template>
                                <template x-if="showNew">
                                    <g><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></g>
                                </template>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <div class="input-wrap">
                        <input :type="showConfirm ? 'text' : 'password'"
                               id="password_confirmation" name="password_confirmation"
                               placeholder="Repeat new password">
                        <button type="button" class="toggle-pw" @click="showConfirm = !showConfirm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <template x-if="!showConfirm">
                                    <g><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></g>
                                </template>
                                <template x-if="showConfirm">
                                    <g><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></g>
                                </template>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Update Password
                </button>
            </form>
        </div>
    </div>

</div>

{{-- ── Card 3: Two-Factor Authentication ──────────────────────────── --}}
<div class="settings-card" style="margin-top:24px;"
     x-data="{ showPass2fa: false, showOtp2fa: false }">
    <div class="settings-card-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            <line x1="12" y1="15" x2="12" y2="17"/>
        </svg>
        Two-Factor Authentication (2FA)
        @if($admin->google2fa_enabled)
            <span style="margin-left:auto;background:#dcfce7;color:#15803d;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:0.04em;">ENABLED</span>
        @else
            <span style="margin-left:auto;background:#fee2e2;color:#dc2626;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:0.04em;">DISABLED</span>
        @endif
    </div>
    <div class="settings-card-body">
        @if($admin->google2fa_enabled)
            {{-- 2FA is ON --}}
            <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;margin-bottom:20px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div>
                    <div style="font-size:13.5px;font-weight:700;color:#15803d;">Your account is protected with Google Authenticator</div>
                    <div style="font-size:12px;color:#16a34a;margin-top:2px;">2FA is active. You'll be asked for an OTP code each time you sign in.</div>
                </div>
            </div>

            <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Disable 2FA</div>
            <form method="POST" action="{{ route('admin.2fa.disable') }}">
                @csrf
                <div class="form-group">
                    <label for="disable_password">Account Password</label>
                    <div class="input-wrap">
                        <input :type="showPass2fa ? 'text' : 'password'"
                               id="disable_password" name="password"
                               placeholder="Enter your account password">
                        <button type="button" class="toggle-pw" @click="showPass2fa = !showPass2fa">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <template x-if="!showPass2fa">
                                    <g><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></g>
                                </template>
                                <template x-if="showPass2fa">
                                    <g><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></g>
                                </template>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="disable_otp">Current OTP Code <span style="color:#94a3b8;font-weight:400;">(from Google Authenticator)</span></label>
                    <input type="text" id="disable_otp" name="one_time_password"
                           maxlength="6" pattern="\d{6}" inputmode="numeric"
                           placeholder="6-digit code"
                           style="font-family:monospace;font-size:18px;letter-spacing:0.3em;text-align:center;">
                    @error('one_time_password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:10px 22px;background:#dc2626;color:#fff;border:none;border-radius:9px;font-size:13.5px;font-weight:600;cursor:pointer;"
                        onclick="return confirm('Are you sure you want to disable 2FA? This will make your account less secure.')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Disable 2FA
                </button>
            </form>
        @else
            {{-- 2FA is OFF --}}
            <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;margin-bottom:20px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>
                    <div style="font-size:13.5px;font-weight:700;color:#dc2626;">Two-factor authentication is not enabled</div>
                    <div style="font-size:12px;color:#ef4444;margin-top:2px;">Add an extra layer of security to protect your admin account.</div>
                </div>
            </div>
            <a href="{{ route('admin.2fa.setup') }}"
               style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#16a34a;color:#fff;border-radius:9px;font-size:13.5px;font-weight:700;text-decoration:none;transition:background 0.15s;"
               onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Enable Two-Factor Authentication
            </a>
            <p style="font-size:12px;color:#94a3b8;margin-top:12px;line-height:1.5;">
                You'll use Google Authenticator to generate a one-time code each time you sign in.
                Requires the free Google Authenticator app on your phone.
            </p>
        @endif
    </div>
</div>

{{-- ── Account Info Card ───────────────────────────────────────────── --}}
<div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,0.05);margin-top:24px;padding:20px 24px;">
    <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:12px;">Account Information</div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;">
            <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Role</div>
            <div style="font-size:14px;font-weight:700;color:#1e293b;">{{ ucfirst($admin->role) }}</div>
        </div>
        <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;">
            <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Account Created</div>
            <div style="font-size:14px;font-weight:700;color:#1e293b;">{{ $admin->created_at->format('d M Y') }}</div>
        </div>
        <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;">
            <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:4px;">Status</div>
            <div style="font-size:14px;font-weight:700;color:{{ $admin->is_active ? '#15803d' : '#dc2626' }};">
                {{ $admin->is_active ? 'Active' : 'Inactive' }}
            </div>
        </div>
    </div>
</div>

@endsection
