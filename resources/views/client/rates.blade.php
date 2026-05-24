@extends('client.layouts.app')
@section('page-title', 'Rate Calculator')

@push('styles')
<style>
.page-wrap { max-width:900px; margin:0 auto; padding:32px 24px 60px; }
.page-heading { font-size:24px; font-weight:800; color:#0f172a; margin-bottom:4px; }
.page-sub { font-size:14px; color:#64748b; margin-bottom:28px; }
.tab-bar { display:flex; gap:2px; background:#f1f5f9; border-radius:12px; padding:4px; width:fit-content; margin-bottom:22px; }
.tab-btn { padding:9px 24px; border-radius:9px; font-size:13.5px; font-weight:600; cursor:pointer; border:none; background:transparent; color:#64748b; transition:all 0.15s; }
.tab-btn.active { background:#fff; color:#1e293b; box-shadow:0 1px 4px rgba(0,0,0,0.1); }
.form-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:28px; box-shadow:0 1px 4px rgba(0,0,0,0.04); margin-bottom:20px; }
.form-section-title { font-size:12px; font-weight:700; color:#64748b; letter-spacing:0.07em; text-transform:uppercase; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
.form-row { display:grid; gap:14px; margin-bottom:16px; }
.form-row-2 { grid-template-columns:1fr 1fr; }
.form-row-3 { grid-template-columns:1fr 1fr 1fr; }
.form-row-4 { grid-template-columns:1fr 1fr 1fr 1fr; }
@media(max-width:640px) { .form-row-2,.form-row-3,.form-row-4 { grid-template-columns:1fr; } }
.form-label { display:block; font-size:12.5px; font-weight:600; color:#374151; margin-bottom:5px; }
.form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; font-family:inherit; }
.form-control:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.radio-group { display:flex; gap:10px; flex-wrap:wrap; }
.radio-card { display:flex; align-items:center; gap:8px; padding:9px 16px; border:1.5px solid #e2e8f0; border-radius:9px; cursor:pointer; font-size:13px; font-weight:500; color:#475569; transition:all 0.15s; }
.radio-card:has(input:checked) { border-color:#2563eb; background:#eff6ff; color:#1d4ed8; }
.radio-card input { accent-color:#2563eb; }
.dim-label { font-size:11.5px; color:#94a3b8; margin-top:3px; }
.btn-calc { width:100%; padding:13px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; transition:background 0.15s; font-family:inherit; margin-top:4px; display:flex; align-items:center; justify-content:center; gap:8px; }
.btn-calc:hover { background:#1d4ed8; }
.btn-calc:disabled { opacity:0.6; cursor:not-allowed; }

/* Result panel */
.result-card { background:#eff6ff; border:1.5px solid #bfdbfe; border-radius:14px; padding:28px; margin-top:20px; animation:fadeUp 0.3s ease; }
@keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
.result-heading { font-size:16px; font-weight:800; color:#1e40af; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
.result-grid { display:grid; grid-template-columns:1.4fr 1fr; gap:24px; }
@media(max-width:640px) { .result-grid { grid-template-columns:1fr; } }
.breakdown-table { width:100%; border-collapse:collapse; font-size:13.5px; }
.breakdown-table td { padding:8px 0; border-bottom:1px solid #bfdbfe; color:#374151; }
.breakdown-table td:last-child { text-align:right; font-weight:600; color:#1e293b; }
.breakdown-table tr:last-child td { border-bottom:none; font-size:15px; font-weight:800; color:#1e40af; border-top:2px solid #93c5fd; padding-top:12px; }
.info-card { background:#fff; border-radius:10px; border:1px solid #bfdbfe; padding:18px; }
.info-row { display:flex; flex-direction:column; margin-bottom:14px; }
.info-row:last-child { margin-bottom:0; }
.info-label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px; }
.info-value { font-size:14px; font-weight:700; color:#1e293b; }
.btn-book { display:inline-flex; align-items:center; gap:8px; margin-top:18px; padding:11px 24px; background:#1e40af; color:#fff; border-radius:9px; font-size:14px; font-weight:700; text-decoration:none; transition:background 0.15s; }
.btn-book:hover { background:#1d4ed8; }

/* Error panel */
.error-card { background:#fef2f2; border:1.5px solid #fecaca; border-radius:12px; padding:16px 20px; margin-top:16px; color:#dc2626; font-size:14px; font-weight:500; animation:fadeUp 0.2s ease; }

[x-cloak] { display:none!important; }
</style>
@endpush

@section('content')
<div class="page-wrap" x-data="{
    tab: 'domestic',
    loading: false,
    result: null,
    error: null,
    calculate(form) {
        this.loading = true; this.result = null; this.error = null;
        const data = Object.fromEntries(new FormData(form));
        data.type = this.tab;
        fetch('/client/rates/calculate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(d => { if(d.success) this.result = d.breakdown; else this.error = d.message; })
        .catch(() => { this.error = 'Something went wrong. Please try again.'; })
        .finally(() => this.loading = false);
    },
    formatService(s) {
        return { express_air:'Express Air', priority_surface:'Priority Surface', economy_surface:'Economy Surface',
                 international_express:'International Express', international_economy:'International Economy' }[s] || s;
    }
}">

<div class="page-heading">💰 Rate Calculator</div>
<p class="page-sub">Get an instant, transparent quote for your shipment — no hidden charges.</p>

@if(!$hasRateCard)
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 18px;color:#b45309;font-size:14px;margin-bottom:20px">
        ⚠️ No rate card assigned to your account. Please contact support to get rates.
    </div>
@endif

{{-- Tab bar --}}
<div class="tab-bar">
    <button class="tab-btn" :class="{ active: tab==='domestic' }" @click="tab='domestic'; result=null; error=null">🇮🇳 Domestic</button>
    <button class="tab-btn" :class="{ active: tab==='international' }" @click="tab='international'; result=null; error=null">🌍 International</button>
</div>

{{-- DOMESTIC TAB --}}
<div x-show="tab === 'domestic'" x-cloak>
<form class="form-card" @submit.prevent="calculate($el)">
    <div class="form-section-title">📦 Shipment Details</div>

    <div class="form-row form-row-2">
        <div>
            <label class="form-label">Origin Pincode *</label>
            <input type="text" name="origin_pincode" class="form-control" placeholder="e.g. 110001" maxlength="6" pattern="\d{6}" required>
        </div>
        <div>
            <label class="form-label">Destination Pincode *</label>
            <input type="text" name="dest_pincode" class="form-control" placeholder="e.g. 400001" maxlength="6" pattern="\d{6}" required>
        </div>
    </div>

    <div class="form-row form-row-2">
        <div>
            <label class="form-label">Actual Weight (kg) *</label>
            <input type="number" name="weight_actual" class="form-control" placeholder="0.5" step="0.1" min="0.1" max="999" required>
        </div>
        <div>
            <label class="form-label">Service Type *</label>
            <select name="service_type" class="form-control" required>
                <option value="express_air">✈️ Express Air (1-2 days)</option>
                <option value="priority_surface">🚛 Priority Surface (3-5 days)</option>
                <option value="economy_surface">📦 Economy Surface (5-7 days)</option>
            </select>
        </div>
    </div>

    <div style="margin-bottom:16px">
        <label class="form-label">Parcel Type *</label>
        <div class="radio-group">
            <label class="radio-card"><input type="radio" name="parcel_type" value="document" checked> 📄 Document</label>
            <label class="radio-card"><input type="radio" name="parcel_type" value="non_document"> 📦 Non-Document</label>
            <label class="radio-card"><input type="radio" name="parcel_type" value="fragile"> 🪟 Fragile</label>
        </div>
    </div>

    <div class="form-section-title" style="margin-top:8px">📐 Dimensions (optional — for volumetric weight)</div>
    <div class="form-row form-row-3">
        <div>
            <label class="form-label">Length (cm)</label>
            <input type="number" name="length" class="form-control" placeholder="0" step="0.1" min="1">
            <div class="dim-label">L</div>
        </div>
        <div>
            <label class="form-label">Width (cm)</label>
            <input type="number" name="width" class="form-control" placeholder="0" step="0.1" min="1">
            <div class="dim-label">W</div>
        </div>
        <div>
            <label class="form-label">Height (cm)</label>
            <input type="number" name="height" class="form-control" placeholder="0" step="0.1" min="1">
            <div class="dim-label">H</div>
        </div>
    </div>

    <button type="submit" class="btn-calc" :disabled="loading">
        <span x-show="!loading">🔍 Calculate Rate</span>
        <span x-show="loading" x-cloak>⏳ Calculating…</span>
    </button>
</form>
</div>

{{-- INTERNATIONAL TAB --}}
<div x-show="tab === 'international'" x-cloak>
<form class="form-card" @submit.prevent="calculate($el)">
    <div class="form-section-title">🌍 International Shipment Details</div>

    <div class="form-row form-row-2">
        <div>
            <label class="form-label">Origin City *</label>
            <input type="text" name="origin_city" class="form-control" placeholder="e.g. Mumbai" required>
        </div>
        <div>
            <label class="form-label">Destination Country Group *</label>
            <select name="dest_country_group" class="form-control" required>
                <option value="">— Select group —</option>
                <option value="SAARC">SAARC (Nepal, Bangladesh, Sri Lanka…)</option>
                <option value="Southeast Asia">Southeast Asia (Thailand, Vietnam, Malaysia…)</option>
                <option value="Middle East">Middle East (UAE, Saudi, Qatar…)</option>
                <option value="Europe">Europe (UK, Germany, France…)</option>
                <option value="North America">North America (USA, Canada)</option>
                <option value="Australia">Australia & NZ</option>
                <option value="Rest of World">Rest of World</option>
            </select>
        </div>
    </div>

    <div class="form-row form-row-2">
        <div>
            <label class="form-label">Actual Weight (kg) *</label>
            <input type="number" name="weight_actual" class="form-control" placeholder="0.5" step="0.1" min="0.1" max="999" required>
        </div>
        <div>
            <label class="form-label">Service Type *</label>
            <select name="service_type" class="form-control" required>
                <option value="international_express">✈️ Express (3-5 days)</option>
                <option value="international_economy">📦 Economy (7-10 days)</option>
            </select>
        </div>
    </div>

    <div style="margin-bottom:16px">
        <label class="form-label">Parcel Type *</label>
        <div class="radio-group">
            <label class="radio-card"><input type="radio" name="parcel_type" value="document" checked> 📄 Document</label>
            <label class="radio-card"><input type="radio" name="parcel_type" value="non_document"> 📦 Non-Document</label>
            <label class="radio-card"><input type="radio" name="parcel_type" value="fragile"> 🪟 Fragile</label>
        </div>
    </div>

    <div class="form-section-title" style="margin-top:8px">📐 Dimensions (optional)</div>
    <div class="form-row form-row-3">
        <div><label class="form-label">Length (cm)</label><input type="number" name="length" class="form-control" placeholder="0" step="0.1" min="1"></div>
        <div><label class="form-label">Width (cm)</label><input type="number" name="width" class="form-control" placeholder="0" step="0.1" min="1"></div>
        <div><label class="form-label">Height (cm)</label><input type="number" name="height" class="form-control" placeholder="0" step="0.1" min="1"></div>
    </div>

    <button type="submit" class="btn-calc" :disabled="loading">
        <span x-show="!loading">🔍 Calculate Rate</span>
        <span x-show="loading" x-cloak>⏳ Calculating…</span>
    </button>
</form>
</div>

{{-- Result --}}
<div class="result-card" x-show="result !== null" x-cloak>
    <div class="result-heading">✅ Your Shipment Quote</div>
    <div class="result-grid">
        <div>
            <table class="breakdown-table">
                <tr><td>Chargeable Weight</td><td x-text="result?.chargeable_weight + ' kg'"></td></tr>
                <tr><td>Actual Weight</td><td x-text="result?.actual_weight + ' kg'"></td></tr>
                <tr x-show="result?.dimensional_weight > 0"><td>Volumetric Weight</td><td x-text="result?.dimensional_weight + ' kg'"></td></tr>
                <tr><td>Base Freight</td><td x-text="'₹' + result?.base_freight?.toFixed(2)"></td></tr>
                <tr><td x-text="'Fuel Surcharge (' + result?.fuel_pct + '%)'"></td><td x-text="'₹' + result?.fuel_surcharge?.toFixed(2)"></td></tr>
                <tr x-show="result?.oda_charge > 0"><td>ODA Charge</td><td x-text="'₹' + result?.oda_charge?.toFixed(2)"></td></tr>
                <tr><td>GST (18%)</td><td x-text="'₹' + result?.gst?.toFixed(2)"></td></tr>
                <tr><td>Estimated Total</td><td x-text="'₹' + result?.total?.toFixed(2)"></td></tr>
            </table>
        </div>
        <div>
            <div class="info-card">
                <div class="info-row" x-show="result?.zone">
                    <div class="info-label">Delivery Zone</div>
                    <div class="info-value" x-text="'Zone ' + result?.zone"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Estimated Transit</div>
                    <div class="info-value" x-text="result?.transit_days"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Service</div>
                    <div class="info-value" x-text="formatService(result?.service_type)"></div>
                </div>
            </div>
            <a href="{{ route('client.book') }}" class="btn-book">📦 Proceed to Book →</a>
        </div>
    </div>
</div>

<div class="error-card" x-show="error !== null" x-cloak>
    ❌ <span x-text="error"></span>
</div>

</div>
@endsection
