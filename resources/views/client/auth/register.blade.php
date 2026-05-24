@extends('client.layouts.app')
@section('page-title', 'Create Account')

@php
$states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu','Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'];
@endphp

@push('styles')
<style>
    .auth-page { background: linear-gradient(135deg,#eff6ff 0%,#f0fdf4 100%); padding: 40px 16px; min-height: calc(100vh - 64px); display: flex; align-items: flex-start; justify-content: center; }
    .auth-card { background:#fff; border-radius:20px; border:1px solid #e2e8f0; box-shadow:0 8px 40px rgba(0,0,0,0.08); width:100%; max-width:860px; padding:40px; }
    .auth-header { text-align:center; margin-bottom:32px; }
    .auth-title { font-size:24px; font-weight:800; color:#0f172a; margin-bottom:6px; }
    .auth-sub { font-size:14px; color:#64748b; }
    .reg-grid { display:grid; grid-template-columns:1fr 1fr; gap:32px; }
    @media(max-width:700px) { .reg-grid { grid-template-columns:1fr; } }
    .col-title { font-size:12px; font-weight:700; color:#64748b; letter-spacing:0.07em; text-transform:uppercase; margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
    .form-group { margin-bottom:16px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px; }
    .required::after { content:' *'; color:#ef4444; }
    .form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; font-family:inherit; }
    .form-control:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
    .form-control.is-invalid { border-color:#ef4444; }
    .form-error { font-size:12px; color:#ef4444; margin-top:4px; font-weight:500; }
    .form-hint { font-size:11.5px; color:#94a3b8; margin-top:3px; }
    .input-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .input-wrap { position:relative; }
    .eye-btn { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; padding:2px; }
    .terms-box { border:1.5px solid #e2e8f0; border-radius:10px; padding:14px; margin:20px 0; }
    .terms-box:has(input:checked) { border-color:#2563eb; background:#eff6ff; }
    .terms-label { display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:13px; color:#475569; }
    .terms-label input { accent-color:#2563eb; width:16px; height:16px; margin-top:2px; flex-shrink:0; }
    .terms-label a { color:#2563eb; font-weight:600; text-decoration:none; }
    .btn-submit { width:100%; padding:13px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; transition:background 0.15s; margin-top:6px; font-family:inherit; }
    .btn-submit:hover { background:#1d4ed8; }
    .auth-footer { text-align:center; margin-top:20px; font-size:13.5px; color:#64748b; }
    .auth-footer a { color:#2563eb; font-weight:600; text-decoration:none; }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div style="font-size:36px;margin-bottom:10px">✨</div>
            <h1 class="auth-title">Create Your Account</h1>
            <p class="auth-sub">Join 10,000+ businesses shipping smarter with CourierPortal</p>
        </div>

        <form method="POST" action="{{ route('client.register.post') }}">
        @csrf
        <div class="reg-grid">

            {{-- LEFT: Personal Info --}}
            <div>
                <div class="col-title">👤 Personal Information</div>

                <div class="form-group">
                    <label class="form-label required">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           placeholder="Your full name" autofocus>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           placeholder="you@company.com">
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">Phone Number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                           placeholder="10-digit mobile number" maxlength="15">
                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group" x-data="{ show: false }">
                    <label class="form-label required">Password</label>
                    <div class="input-wrap">
                        <input :type="show ? 'text' : 'password'" name="password"
                               class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="Minimum 8 characters" style="padding-right:44px">
                        <button type="button" class="eye-btn" @click="show = !show" tabindex="-1">
                            <svg x-show="!show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="form-control" placeholder="Re-enter password">
                </div>
            </div>

            {{-- RIGHT: Company Info --}}
            <div>
                <div class="col-title">🏢 Company Information</div>

                <div class="form-group">
                    <label class="form-label required">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}"
                           class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}"
                           placeholder="Registered company name">
                    @error('company_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">GSTIN <span style="color:#94a3b8;font-weight:400">(optional)</span></label>
                    <input type="text" name="gstin" value="{{ old('gstin') }}"
                           class="form-control {{ $errors->has('gstin') ? 'is-invalid' : '' }}"
                           placeholder="22AAAAA0000A1Z5" maxlength="15" style="text-transform:uppercase">
                    @error('gstin') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">Address</label>
                    <textarea name="address" rows="2"
                              class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}"
                              placeholder="Street address, building, area">{{ old('address') }}</textarea>
                    @error('address') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="input-grid-2">
                    <div class="form-group">
                        <label class="form-label required">City</label>
                        <input type="text" name="city" value="{{ old('city') }}"
                               class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}"
                               placeholder="City">
                        @error('city') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Pincode</label>
                        <input type="text" name="pincode" value="{{ old('pincode') }}"
                               class="form-control {{ $errors->has('pincode') ? 'is-invalid' : '' }}"
                               placeholder="6-digit pincode" maxlength="6">
                        @error('pincode') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">State</label>
                    <select name="state" class="form-control {{ $errors->has('state') ? 'is-invalid' : '' }}">
                        <option value="">— Select State —</option>
                        @foreach($states as $s)
                            <option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('state') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Account Type</label>
                    <div class="form-control" style="background:#eff6ff;border-color:#bfdbfe;color:#1d4ed8;font-weight:600;cursor:default">💳 Prepaid</div>
                    <div class="form-hint">Credit accounts require admin approval. Start with Prepaid.</div>
                </div>
            </div>
        </div>

        {{-- Terms --}}
        <div class="terms-box">
            <label class="terms-label">
                <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a> of CourierPortal. I confirm that the information provided is accurate.</span>
            </label>
            @error('terms') <div class="form-error" style="margin-top:8px">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn-submit">✨ Create Account — It's Free</button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('client.login') }}">Sign in here</a>
        </div>
    </div>
</div>
@endsection
