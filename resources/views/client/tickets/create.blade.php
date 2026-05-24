@extends('client.layouts.app')
@section('page-title', 'Raise a Support Query')

@push('styles')
<style>
.page-wrap { max-width:720px; margin:0 auto; padding:28px 24px 60px; }
.back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:#64748b; text-decoration:none; margin-bottom:18px; }
.back-link:hover { color:#2563eb; }
.page-heading { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:22px; }
.form-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:28px; }
.form-group { margin-bottom:18px; }
.form-label { display:block; font-size:12.5px; font-weight:600; color:#374151; margin-bottom:5px; }
.required::after { content:' *'; color:#ef4444; }
.form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; font-family:inherit; }
.form-control:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.is-invalid { border-color:#ef4444!important; }
.error-msg { font-size:12px; color:#ef4444; margin-top:4px; }
.hint { font-size:12px; color:#94a3b8; margin-top:4px; }
.char-count { font-size:12px; color:#94a3b8; float:right; }
.btn-submit { width:100%; padding:13px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; font-family:inherit; }
.btn-submit:hover { background:#1d4ed8; }
</style>
@endpush

@section('content')
<div class="page-wrap">
    <a href="{{ route('client.tickets') }}" class="back-link">← Back to Tickets</a>
    <div class="page-heading">🎫 Raise a Support Query</div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;color:#dc2626;font-size:14px;margin-bottom:18px">
            ❌ Please fix the errors below.
        </div>
    @endif

    <form class="form-card" method="POST" action="{{ route('client.tickets.store') }}" enctype="multipart/form-data"
          x-data="{ charCount: 0, awbMode: 'select' }">
    @csrf

        <div class="form-group">
            <label class="form-label required">Category</label>
            <select name="category" class="form-control {{ $errors->has('category') ? 'is-invalid' : '' }}" required>
                <option value="">— Select category —</option>
                <option value="delayed_shipment"   {{ old('category')==='delayed_shipment'   ? 'selected':'' }}>📦 Delayed Shipment</option>
                <option value="damage"             {{ old('category')==='damage'             ? 'selected':'' }}>💔 Damage / Loss</option>
                <option value="wrong_delivery"     {{ old('category')==='wrong_delivery'     ? 'selected':'' }}>❌ Wrong Delivery</option>
                <option value="invoice_issue"      {{ old('category')==='invoice_issue'      ? 'selected':'' }}>🧾 Invoice Issue</option>
                <option value="rate_query"         {{ old('category')==='rate_query'         ? 'selected':'' }}>💰 Rate Query</option>
                <option value="other"              {{ old('category')==='other'              ? 'selected':'' }}>💬 Other</option>
            </select>
            @error('category')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Related AWB Number (optional)</label>
            <div style="display:flex;gap:8px;margin-bottom:8px">
                <label style="font-size:13px;cursor:pointer;color:#475569">
                    <input type="radio" x-model="awbMode" value="select"> Choose from recent bookings
                </label>
                <label style="font-size:13px;cursor:pointer;color:#475569">
                    <input type="radio" x-model="awbMode" value="type"> Type manually
                </label>
            </div>
            <div x-show="awbMode === 'select'">
                <select name="awb_number" class="form-control">
                    <option value="">— Select AWB —</option>
                    @foreach($recentBookings as $awb)
                        <option value="{{ $awb }}" {{ old('awb_number')===$awb ? 'selected':'' }}>{{ $awb }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="awbMode === 'type'" x-cloak>
                <input type="text" name="awb_number_manual" class="form-control" placeholder="e.g. CP2505001234" style="text-transform:uppercase">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label required">Subject</label>
            <input type="text" name="subject" value="{{ old('subject') }}"
                   class="form-control {{ $errors->has('subject') ? 'is-invalid' : '' }}"
                   placeholder="Brief summary of your issue" required>
            @error('subject')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label required">
                Description
                <span class="char-count" x-text="charCount + ' / 2000'"></span>
            </label>
            <textarea name="description" rows="5"
                      class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                      placeholder="Describe your issue in detail (minimum 20 characters)…"
                      x-on:input="charCount = $el.value.length"
                      required>{{ old('description') }}</textarea>
            <div class="hint">Minimum 20 characters required.</div>
            @error('description')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Attachment (optional)</label>
            <input type="file" name="attachment" class="form-control {{ $errors->has('attachment') ? 'is-invalid' : '' }}"
                   accept=".pdf,.jpg,.jpeg,.png">
            <div class="hint">PDF, JPG, PNG — max 5MB</div>
            @error('attachment')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn-submit">Submit Ticket</button>
    </form>
</div>
@endsection
