@php
$indianStates = [
    'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
    'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
    'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram',
    'Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana',
    'Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
    'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu',
    'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry',
];
@endphp

@extends('admin.layouts.app')

@section('page-title')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    Edit Client — {{ $client->company_name }}
@endsection

@push('styles')
<style>
    .form-page-header { display:flex; align-items:center; justify-content:flex-end; margin-bottom:22px; gap:12px; flex-wrap:wrap; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
    @media(max-width:768px) { .form-grid { grid-template-columns:1fr; } }
    .form-section { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
    .section-title { font-size:13px; font-weight:700; color:#64748b; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:7px; }
    .form-group { margin-bottom:16px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
    .required::after { content:' *'; color:#ef4444; }
    .form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; }
    .form-control:focus { border-color:#3b82f6; background:#fff; box-shadow:0 0 0 3px rgba(59,130,246,0.1); }
    .form-control.is-invalid { border-color:#ef4444; }
    .form-error { font-size:12px; color:#ef4444; margin-top:4px; font-weight:500; }
    .form-hint { font-size:11.5px; color:#94a3b8; margin-top:4px; }
    .radio-group { display:flex; gap:16px; flex-wrap:wrap; }
    .radio-label { display:flex; align-items:center; gap:8px; padding:9px 16px; border:1.5px solid #e2e8f0; border-radius:9px; cursor:pointer; font-size:13.5px; font-weight:500; transition:all 0.15s; flex:1; }
    .radio-label:has(input:checked) { border-color:#3b82f6; background:#eff6ff; color:#1d4ed8; }
    .radio-label input { accent-color:#3b82f6; }
    .form-footer { display:flex; gap:12px; justify-content:flex-end; margin-top:24px; padding-top:20px; border-top:1px solid #f1f5f9; grid-column:1 / -1; }
    .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 22px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:opacity 0.15s; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { opacity:0.88; }
    .btn-secondary { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
    .btn-secondary:hover { background:#e2e8f0; }
    [x-cloak] { display:none !important; }
</style>
@endpush

@section('content')
<div class="form-page-header">
    <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-secondary">← Back to Client</a>
</div>

<form method="POST" action="{{ route('admin.clients.update', $client->id) }}">
@csrf
@method('PATCH')
<div class="form-grid">

    {{-- ── LEFT: Account Info ──────────────────────────────────── --}}
    <div class="form-section">
        <div class="section-title">👤 Account Information</div>

        <div class="form-group">
            <label class="form-label required">Contact Name</label>
            <input type="text" name="name" value="{{ old('name', $client->user?->name) }}"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label required">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $client->user?->email) }}"
                   class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label required">Phone Number</label>
            <input type="text" name="phone" value="{{ old('phone', $client->user?->phone) }}"
                   class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}">
            @error('phone') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">New Password</label>
            <input type="password" name="password"
                   class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="Leave blank to keep current password">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation"
                   class="form-control" placeholder="Re-enter new password">
        </div>
    </div>

    {{-- ── RIGHT: Company Info ─────────────────────────────────── --}}
    <div class="form-section" x-data="{ accountType: '{{ old('account_type', $client->account_type) }}' }">
        <div class="section-title">🏢 Company Information</div>

        <div class="form-group">
            <label class="form-label required">Company Name</label>
            <input type="text" name="company_name" value="{{ old('company_name', $client->company_name) }}"
                   class="form-control {{ $errors->has('company_name') ? 'is-invalid' : '' }}">
            @error('company_name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">GSTIN</label>
            <input type="text" name="gstin" value="{{ old('gstin', $client->gstin) }}"
                   class="form-control {{ $errors->has('gstin') ? 'is-invalid' : '' }}"
                   style="text-transform:uppercase" maxlength="15">
            @error('gstin') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label required">Address</label>
            <textarea name="address" rows="2" class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}">{{ old('address', $client->address) }}</textarea>
            @error('address') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label class="form-label required">City</label>
                <input type="text" name="city" value="{{ old('city', $client->city) }}"
                       class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}">
                @error('city') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label required">Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode', $client->pincode) }}"
                       class="form-control {{ $errors->has('pincode') ? 'is-invalid' : '' }}" maxlength="6">
                @error('pincode') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label required">State</label>
            <select name="state" class="form-control {{ $errors->has('state') ? 'is-invalid' : '' }}">
                <option value="">— Select State —</option>
                @foreach($indianStates as $state)
                    <option value="{{ $state }}" {{ old('state', $client->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                @endforeach
            </select>
            @error('state') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label required">Account Type</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="account_type" value="prepaid" x-model="accountType"
                           {{ old('account_type', $client->account_type) === 'prepaid' ? 'checked' : '' }}>
                    💳 Prepaid
                </label>
                <label class="radio-label">
                    <input type="radio" name="account_type" value="credit" x-model="accountType"
                           {{ old('account_type', $client->account_type) === 'credit' ? 'checked' : '' }}>
                    📋 Credit
                </label>
            </div>
            @error('account_type') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group" x-show="accountType === 'credit'" x-cloak>
            <label class="form-label required">Credit Limit (₹)</label>
            <input type="number" name="credit_limit"
                   value="{{ old('credit_limit', $client->credit_limit) }}"
                   class="form-control {{ $errors->has('credit_limit') ? 'is-invalid' : '' }}"
                   min="0" step="0.01">
            @error('credit_limit') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Rate Card</label>
            <select name="rate_card_id" class="form-control {{ $errors->has('rate_card_id') ? 'is-invalid' : '' }}">
                <option value="">— No Rate Card —</option>
                @foreach($rateCards as $card)
                    <option value="{{ $card->id }}"
                        {{ old('rate_card_id', $client->rate_card_id) == $card->id ? 'selected' : '' }}>
                        {{ $card->name }}{{ $card->is_default ? ' ★ Default' : '' }}
                    </option>
                @endforeach
            </select>
            @error('rate_card_id') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Footer --}}
    <div class="form-footer">
        <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Changes
        </button>
    </div>

</div>
</form>
@endsection
