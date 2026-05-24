@extends('client.layouts.app')
@section('page-title', 'Forgot Password')

@push('styles')
<style>
    .auth-page { min-height: calc(100vh - 64px); display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#eff6ff,#f0fdf4); padding:40px 16px; }
    .auth-card { background:#fff; border-radius:20px; border:1px solid #e2e8f0; box-shadow:0 8px 40px rgba(0,0,0,0.08); width:100%; max-width:420px; padding:40px; }
    .auth-header { text-align:center; margin-bottom:28px; }
    .auth-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .auth-sub { font-size:13.5px; color:#64748b; line-height:1.5; }
    .form-group { margin-bottom:18px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
    .form-control { width:100%; padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:14px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; font-family:inherit; }
    .form-control:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
    .form-error { font-size:12px; color:#ef4444; margin-top:4px; }
    .btn-submit { width:100%; padding:12px; background:#2563eb; color:#fff; border:none; border-radius:9px; font-size:14.5px; font-weight:700; cursor:pointer; font-family:inherit; }
    .btn-submit:hover { background:#1d4ed8; }
    .back-link { display:block; text-align:center; margin-top:18px; font-size:13px; color:#64748b; text-decoration:none; }
    .back-link:hover { color:#2563eb; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div style="font-size:36px;margin-bottom:12px">🔑</div>
            <h1 class="auth-title">Forgot Password?</h1>
            <p class="auth-sub">Enter your registered email and we'll send you a reset link.</p>
        </div>
        <form method="POST" action="{{ route('client.password.email') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                       placeholder="you@company.com" autofocus>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn-submit">Send Reset Link</button>
        </form>
        <a href="{{ route('client.login') }}" class="back-link">← Back to Sign In</a>
    </div>
</div>
@endsection
