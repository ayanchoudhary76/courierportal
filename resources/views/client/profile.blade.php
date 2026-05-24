@extends('client.layouts.app')
@section('page-title', 'My Profile')

@push('styles')
<style>
.page-wrap { max-width:900px; margin:0 auto; padding:32px 24px 60px; }
.page-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:24px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
@media(max-width:700px) { .form-grid { grid-template-columns:1fr; } }
.form-section { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
.section-title { font-size:12px; font-weight:700; color:#64748b; letter-spacing:0.07em; text-transform:uppercase; margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
.form-group { margin-bottom:16px; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px; }
.required::after { content:' *'; color:#ef4444; }
.form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; font-family:inherit; }
.form-control:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.form-control.is-invalid { border-color:#ef4444; }
.form-error { font-size:12px; color:#ef4444; margin-top:4px; font-weight:500; }
.input-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.form-footer { grid-column:1/-1; display:flex; justify-content:flex-end; gap:12px; padding-top:20px; border-top:1px solid #f1f5f9; margin-top:8px; }
.btn { display:inline-flex; align-items:center; gap:7px; padding:10px 22px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; font-family:inherit; }
.btn-primary { background:#2563eb; color:#fff; }
.btn-primary:hover { background:#1d4ed8; }
.btn-secondary { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
.readonly-field { background:#f1f5f9; color:#64748b; cursor:default; }
</style>
@endpush

@php
$states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu','Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'];
@endphp

@section('content')
<div class="page-wrap">
    <div class="page-title">👤 My Profile</div>

    <form method="POST" action="{{ route('client.profile.update') }}">
    @csrf @method('PUT')
    <div class="form-grid">

        <div class="form-section">
            <div class="section-title">Personal Information</div>

            <div class="form-group">
                <label class="form-label required">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" value="{{ $user->email }}" class="form-control readonly-field" readonly>
                <div style="font-size:11.5px;color:#94a3b8;margin-top:3px">Email cannot be changed. Contact support.</div>
            </div>

            <div class="form-group">
                <label class="form-label required">Phone Number</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" maxlength="15">
                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Account Type</label>
                <input type="text" value="{{ ucfirst($client?->account_type ?? 'prepaid') }}" class="form-control readonly-field" readonly>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title">Company Information</div>

            <div class="form-group">
                <label class="form-label required">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name', $client?->company_name) }}"
                       class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}">
                @error('company_name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">GSTIN</label>
                <input type="text" name="gstin" value="{{ old('gstin', $client?->gstin) }}"
                       class="form-control {{ $errors->has('gstin') ? 'is-invalid' : '' }}"
                       placeholder="22AAAAA0000A1Z5" style="text-transform:uppercase" maxlength="15">
                @error('gstin') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label required">Address</label>
                <textarea name="address" rows="2" class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}">{{ old('address', $client?->address) }}</textarea>
                @error('address') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="input-grid-2">
                <div class="form-group">
                    <label class="form-label required">City</label>
                    <input type="text" name="city" value="{{ old('city', $client?->city) }}"
                           class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}">
                    @error('city') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label required">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $client?->pincode) }}"
                           class="form-control {{ $errors->has('pincode') ? 'is-invalid' : '' }}" maxlength="6">
                    @error('pincode') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label required">State</label>
                <select name="state" class="form-control {{ $errors->has('state') ? 'is-invalid' : '' }}">
                    <option value="">— Select State —</option>
                    @foreach($states as $s)
                        <option value="{{ $s }}" {{ old('state', $client?->state) === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                @error('state') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Save Changes
            </button>
        </div>
    </div>
    </form>
</div>
@endsection
