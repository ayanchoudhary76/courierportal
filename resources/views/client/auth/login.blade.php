@extends('client.layouts.app')
@section('page-title', 'Sign In')

@push('styles')
<style>
    .auth-page { min-height: calc(100vh - 64px); display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); padding: 40px 16px; }
    .auth-card { background: #fff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 8px 40px rgba(0,0,0,0.08); width: 100%; max-width: 440px; padding: 40px; }
    .auth-header { text-align: center; margin-bottom: 32px; }
    .auth-icon { font-size: 40px; margin-bottom: 12px; }
    .auth-title { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
    .auth-sub { font-size: 14px; color: #64748b; }
    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .input-wrap { position: relative; }
    .form-control {
        width: 100%; padding: 11px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
        font-size: 14px; color: #0f172a; background: #f8fafc; outline: none;
        transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit;
    }
    .form-control:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
    .form-control.is-invalid { border-color: #ef4444; }
    .form-error { font-size: 12px; color: #ef4444; margin-top: 5px; font-weight: 500; }
    .eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 2px; }
    .form-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
    .checkbox-label { display: flex; align-items: center; gap: 7px; font-size: 13px; color: #475569; cursor: pointer; }
    .checkbox-label input { accent-color: #2563eb; width: 15px; height: 15px; }
    .forgot-link { font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500; }
    .forgot-link:hover { text-decoration: underline; }
    .btn-submit { width: 100%; padding: 13px; background: #2563eb; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.15s, transform 0.1s; font-family: inherit; }
    .btn-submit:hover { background: #1d4ed8; }
    .btn-submit:active { transform: scale(0.99); }
    .divider { text-align: center; margin: 20px 0; color: #94a3b8; font-size: 13px; position: relative; }
    .divider::before, .divider::after { content: ''; position: absolute; top: 50%; width: 40%; height: 1px; background: #e2e8f0; }
    .divider::before { left: 0; }
    .divider::after { right: 0; }
    .auth-footer { text-align: center; font-size: 13.5px; color: #64748b; }
    .auth-footer a { color: #2563eb; font-weight: 600; text-decoration: none; }
    .auth-footer a:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">🔐</div>
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-sub">Sign in to your CourierPortal account</p>
        </div>

        <form method="POST" action="{{ route('client.login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       placeholder="you@company.com" autofocus required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group" x-data="{ show: false }">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <input :type="show ? 'text' : 'password'" name="password"
                           class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="Enter your password" required style="padding-right:44px">
                    <button type="button" class="eye-btn" @click="show = !show" tabindex="-1">
                        <svg x-show="!show" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg x-show="show" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                    </button>
                </div>
                @error('password') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="{{ route('client.password.request') }}" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-submit">Sign In →</button>
        </form>

        <div class="divider">or</div>
        <div class="auth-footer">
            Don't have an account? <a href="{{ route('client.register') }}">Register free</a>
        </div>
    </div>
</div>
@endsection
