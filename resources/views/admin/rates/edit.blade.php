@extends('admin.layouts.app')

@section('page-title')
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    Edit — {{ $rateCard->name }}
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
    .checkbox-label { display:flex; align-items:center; gap:10px; cursor:pointer; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:9px; transition:all 0.15s; }
    .checkbox-label:has(input:checked) { border-color:#f59e0b; background:#fffbeb; }
    .checkbox-label input { accent-color:#f59e0b; width:16px; height:16px; cursor:pointer; }
    .checkbox-text { font-size:13.5px; font-weight:600; color:#374151; }
    .checkbox-sub { font-size:12px; color:#94a3b8; margin-top:2px; }
    .info-note { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 16px; font-size:13px; color:#15803d; display:flex; gap:10px; align-items:flex-start; }
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
    <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
        <a href="{{ route('admin.rates.show', $rateCard) }}" class="btn btn-secondary" style="padding:8px 14px;font-size:13px">← Back to Rate Card</a>
    </div>

    <form method="POST" action="{{ route('admin.rates.update', $rateCard) }}">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="form-title">📋 Edit Rate Card</div>

        <div class="form-group">
            <label class="form-label required">Rate Card Name</label>
            <input type="text" name="name" value="{{ old('name', $rateCard->name) }}"
                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3"
                      class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}">{{ old('description', $rateCard->description) }}</textarea>
            @error('description') <div class="form-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Default Rate Card</label>
            <label class="checkbox-label">
                <input type="checkbox" name="is_default" value="1"
                       {{ old('is_default', $rateCard->is_default) ? 'checked' : '' }}>
                <div>
                    <div class="checkbox-text">⭐ Set as Default</div>
                    <div class="checkbox-sub">Checking this will unset any other default rate card</div>
                </div>
            </label>
        </div>
    </div>

    <div class="info-note">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
        <div>To add or edit <strong>rate matrix rows</strong> and <strong>international rates</strong>, use the
        <a href="{{ route('admin.rates.show', $rateCard) }}" style="color:#15803d;font-weight:600">Rate Card detail page</a>.</div>
    </div>

    <div class="form-footer">
        <a href="{{ route('admin.rates.show', $rateCard) }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Save Changes
        </button>
    </div>
    </form>
</div>
@endsection
