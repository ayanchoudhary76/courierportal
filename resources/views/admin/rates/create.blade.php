@extends('admin.layouts.app')

@section('page-title')
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Create Rate Card
@endsection

@push('styles')
<style>
    .form-container { max-width:640px; }
    .form-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:30px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:20px; }
    .form-title { font-size:13px; font-weight:700; color:#64748b; letter-spacing:0.06em; text-transform:uppercase; margin-bottom:20px; padding-bottom:12px; border-bottom:1px solid #f1f5f9; }
    .form-group { margin-bottom:18px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
    .required::after { content:' *'; color:#ef4444; }
    .form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; }
    .form-control:focus { border-color:#3b82f6; background:#fff; box-shadow:0 0 0 3px rgba(59,130,246,0.1); }
    .form-control.is-invalid { border-color:#ef4444; }
    .form-error { font-size:12px; color:#ef4444; margin-top:4px; font-weight:500; }
    .form-hint { font-size:12px; color:#94a3b8; margin-top:4px; }

    .checkbox-label { display:flex; align-items:center; gap:10px; cursor:pointer; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:9px; transition:all 0.15s; }
    .checkbox-label:has(input:checked) { border-color:#f59e0b; background:#fffbeb; }
    .checkbox-label input { accent-color:#f59e0b; width:16px; height:16px; cursor:pointer; }
    .checkbox-text { font-size:13.5px; font-weight:600; color:#374151; }
    .checkbox-sub { font-size:12px; color:#94a3b8; margin-top:2px; }

    .info-note { background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 16px; font-size:13px; color:#1d4ed8; display:flex; gap:10px; align-items:flex-start; }

    .form-footer { display:flex; gap:12px; justify-content:flex-end; margin-top:22px; }
    .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 22px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:opacity 0.15s; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { opacity:0.88; }
    .btn-secondary { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
    .btn-secondary:hover { background:#e2e8f0; }
</style>
@endpush

@section('content')
<div class="form-container">
    <div style="margin-bottom:16px">
        <a href="{{ route('admin.rates.index') }}" class="btn btn-secondary" style="padding:8px 14px;font-size:13px">← Back to Rate Cards</a>
    </div>

    <form method="POST" action="{{ route('admin.rates.store') }}">
    @csrf
    <div class="form-card">
        <div class="form-title">📋 Rate Card Information</div>

        <div class="form-group">
            <label class="form-label required">Rate Card Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   placeholder="e.g. Standard Domestic, Premium Air Express">
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Description <span style="color:#94a3b8;font-weight:400">(optional)</span></label>
            <textarea name="description" rows="3"
                      class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"
                      placeholder="Brief description of when this rate card applies…">{{ old('description') }}</textarea>
            @error('description') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Default Rate Card</label>
            <label class="checkbox-label">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                <div>
                    <div class="checkbox-text">⭐ Set as Default</div>
                    <div class="checkbox-sub">New clients without an assigned rate card will use this one</div>
                </div>
            </label>
            @error('is_default') <div class="form-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="info-note">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.5" fill="currentColor"/></svg>
        <div>
            <strong>Note:</strong> After creating the rate card, you will be taken to the detail page where you can add
            domestic rate matrix rows (by service type, weight slab, and zone) and international pricing.
        </div>
    </div>

    <div class="form-footer">
        <a href="{{ route('admin.rates.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Create Rate Card
        </button>
    </div>
    </form>
</div>
@endsection
