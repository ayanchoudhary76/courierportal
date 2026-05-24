@extends('client.layouts.app')
@section('page-title', 'Book a Shipment')

@push('styles')
<style>
.wizard-wrap { max-width:820px; margin:0 auto; padding:28px 24px 60px; }
/* Step indicator */
.steps-bar { display:flex; align-items:center; margin-bottom:32px; gap:0; }
.step-node { display:flex; flex-direction:column; align-items:center; flex:1; position:relative; }
.step-node:not(:last-child)::after { content:''; position:absolute; top:18px; left:50%; width:100%; height:2px; background:#e2e8f0; z-index:0; }
.step-node.done::after { background:#2563eb; }
.step-circle { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; position:relative; z-index:1; border:2px solid #e2e8f0; background:#fff; color:#94a3b8; transition:all 0.2s; }
.step-circle.active { border-color:#2563eb; background:#2563eb; color:#fff; box-shadow:0 0 0 4px rgba(37,99,235,0.15); }
.step-circle.done { border-color:#2563eb; background:#2563eb; color:#fff; }
.step-label { font-size:11px; font-weight:600; color:#94a3b8; margin-top:6px; text-align:center; }
.step-label.active { color:#2563eb; }
.step-label.done { color:#2563eb; }

/* Cards */
.step-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:28px; box-shadow:0 1px 4px rgba(0,0,0,0.04); margin-bottom:16px; }
.step-title { font-size:17px; font-weight:800; color:#1e293b; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
.form-row-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
@media(max-width:600px) { .form-row-2,.form-row-3 { grid-template-columns:1fr; } }
.form-group { margin-bottom:14px; }
.form-label { display:block; font-size:12.5px; font-weight:600; color:#374151; margin-bottom:5px; }
.required::after { content:' *'; color:#ef4444; }
.form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; transition:border-color 0.2s; font-family:inherit; }
.form-control:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.form-control.is-invalid { border-color:#ef4444; }
.radio-group { display:flex; gap:10px; flex-wrap:wrap; margin-top:4px; }
.radio-card { display:flex; align-items:center; gap:7px; padding:9px 16px; border:1.5px solid #e2e8f0; border-radius:9px; cursor:pointer; font-size:13px; font-weight:500; color:#475569; transition:all 0.15s; }
.radio-card:has(input:checked) { border-color:#2563eb; background:#eff6ff; color:#1d4ed8; }
.radio-card input { accent-color:#2563eb; }

/* Step nav buttons */
.step-footer { display:flex; justify-content:space-between; margin-top:20px; gap:10px; }
.btn { display:inline-flex; align-items:center; gap:7px; padding:11px 24px; border-radius:9px; font-size:14px; font-weight:600; cursor:pointer; border:none; text-decoration:none; font-family:inherit; transition:all 0.15s; }
.btn-primary { background:#2563eb; color:#fff; }
.btn-primary:hover { background:#1d4ed8; }
.btn-success { background:#15803d; color:#fff; }
.btn-success:hover { background:#166534; }
.btn-ghost { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
.btn-ghost:hover { background:#e2e8f0; }

/* Review summary */
.review-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
@media(max-width:600px) { .review-grid { grid-template-columns:1fr; } }
.review-section { background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0; padding:16px; }
.review-section-title { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:10px; }
.review-row { display:flex; gap:6px; margin-bottom:6px; }
.review-label { font-size:12px; color:#64748b; min-width:80px; }
.review-val { font-size:12.5px; font-weight:600; color:#1e293b; }
.inline-error { font-size:12px; color:#ef4444; margin-top:4px; font-weight:500; }

[x-cloak] { display:none!important; }
</style>
@endpush

@section('content')
<div class="wizard-wrap" x-data="{
    step: 1,
    errors: {},
    form: {
        sender_name: '{{ $client?->user?->name }}',
        sender_address: '{{ $client?->address }}',
        sender_pincode: '{{ $prefill['origin_pincode'] }}',
        sender_phone: '{{ $client?->user?->phone }}',
        receiver_name: '',
        receiver_address: '',
        receiver_city: '',
        receiver_pincode: '{{ $prefill['dest_pincode'] }}',
        receiver_phone: '',
        receiver_email: '',
        service_type: '{{ $prefill['service_type'] }}',
        parcel_type: 'non_document',
        weight_actual: '{{ $prefill['weight'] }}',
        length: '', width: '', height: '',
        declared_value: '',
        pieces: 1,
        special_instructions: '',
        pickup_type: 'door_pickup',
        pickup_date: '',
        pickup_slot: '9-11am',
        payment_mode: 'bill_to_account',
    },
    validateStep(n) {
        this.errors = {};
        if(n===1) {
            if(!this.form.sender_name) this.errors.sender_name='Required';
            if(!this.form.sender_address) this.errors.sender_address='Required';
            if(!/^\d{6}$/.test(this.form.sender_pincode)) this.errors.sender_pincode='6-digit pincode required';
            if(!this.form.sender_phone) this.errors.sender_phone='Required';
        }
        if(n===2) {
            if(!this.form.receiver_name) this.errors.receiver_name='Required';
            if(!this.form.receiver_address) this.errors.receiver_address='Required';
            if(!/^\d{6}$/.test(this.form.receiver_pincode)) this.errors.receiver_pincode='6-digit pincode required';
            if(!this.form.receiver_phone) this.errors.receiver_phone='Required';
        }
        if(n===3) {
            if(!this.form.weight_actual) this.errors.weight_actual='Required';
        }
        return Object.keys(this.errors).length === 0;
    },
    next(n) { if(this.validateStep(n)) this.step = n+1; else window.scrollTo({top:0,behavior:'smooth'}); },
    prev(n) { this.step = n-1; },
    serviceLabel(s) {
        return { express_air:'Express Air', priority_surface:'Priority Surface', economy_surface:'Economy Surface',
                 international_express:'International Express', international_economy:'International Economy' }[s] || s;
    }
}">

{{-- Step indicator --}}
<div class="steps-bar">
    @php $stepLabels = ['Sender','Receiver','Parcel','Pickup','Review','Done']; @endphp
    @foreach($stepLabels as $i => $lbl)
    <div class="step-node"
         :class="{ 'done': step > {{ $i+1 }} }">
        <div class="step-circle"
             :class="{ 'active': step === {{ $i+1 }}, 'done': step > {{ $i+1 }} }">
            <span x-show="step <= {{ $i+1 }}">{{ $i+1 }}</span>
            <span x-show="step > {{ $i+1 }}" x-cloak>✓</span>
        </div>
        <div class="step-label"
             :class="{ 'active': step === {{ $i+1 }}, 'done': step > {{ $i+1 }} }">{{ $lbl }}</div>
    </div>
    @endforeach
</div>

<form method="POST" action="{{ route('client.book.store') }}">
@csrf

{{-- ── Step 1: Sender ──────────────────────────────────────────────── --}}
<div x-show="step === 1">
    <div class="step-card">
        <div class="step-title">📤 Step 1 — Sender Details</div>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label required">Sender Name</label>
                <input type="text" name="sender_name" x-model="form.sender_name" class="form-control" :class="{'is-invalid': errors.sender_name}">
                <div class="inline-error" x-show="errors.sender_name" x-text="errors.sender_name" x-cloak></div>
            </div>
            <div class="form-group">
                <label class="form-label required">Phone</label>
                <input type="tel" name="sender_phone" x-model="form.sender_phone" class="form-control" :class="{'is-invalid': errors.sender_phone}">
                <div class="inline-error" x-show="errors.sender_phone" x-text="errors.sender_phone" x-cloak></div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label required">Address</label>
            <textarea name="sender_address" x-model="form.sender_address" class="form-control" rows="2" :class="{'is-invalid': errors.sender_address}"></textarea>
            <div class="inline-error" x-show="errors.sender_address" x-text="errors.sender_address" x-cloak></div>
        </div>
        <div class="form-group" style="max-width:200px">
            <label class="form-label required">Pincode</label>
            <input type="text" name="sender_pincode" x-model="form.sender_pincode" class="form-control" maxlength="6" :class="{'is-invalid': errors.sender_pincode}">
            <div class="inline-error" x-show="errors.sender_pincode" x-text="errors.sender_pincode" x-cloak></div>
        </div>
    </div>
    <div class="step-footer">
        <div></div>
        <button type="button" class="btn btn-primary" @click="next(1)">Next → Receiver</button>
    </div>
</div>

{{-- ── Step 2: Receiver ────────────────────────────────────────────── --}}
<div x-show="step === 2" x-cloak>
    <div class="step-card">
        <div class="step-title">📥 Step 2 — Receiver Details</div>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label required">Receiver Name</label>
                <input type="text" name="receiver_name" x-model="form.receiver_name" class="form-control" :class="{'is-invalid': errors.receiver_name}">
                <div class="inline-error" x-show="errors.receiver_name" x-text="errors.receiver_name" x-cloak></div>
            </div>
            <div class="form-group">
                <label class="form-label required">Phone</label>
                <input type="tel" name="receiver_phone" x-model="form.receiver_phone" class="form-control" :class="{'is-invalid': errors.receiver_phone}">
                <div class="inline-error" x-show="errors.receiver_phone" x-text="errors.receiver_phone" x-cloak></div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label required">Delivery Address</label>
            <textarea name="receiver_address" x-model="form.receiver_address" class="form-control" rows="2" :class="{'is-invalid': errors.receiver_address}"></textarea>
            <div class="inline-error" x-show="errors.receiver_address" x-text="errors.receiver_address" x-cloak></div>
        </div>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="receiver_city" x-model="form.receiver_city" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label required">Pincode</label>
                <input type="text" name="receiver_pincode" x-model="form.receiver_pincode" class="form-control" maxlength="6" :class="{'is-invalid': errors.receiver_pincode}">
                <div class="inline-error" x-show="errors.receiver_pincode" x-text="errors.receiver_pincode" x-cloak></div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Email (optional)</label>
            <input type="email" name="receiver_email" x-model="form.receiver_email" class="form-control">
        </div>
    </div>
    <div class="step-footer">
        <button type="button" class="btn btn-ghost" @click="prev(2)">← Sender</button>
        <button type="button" class="btn btn-primary" @click="next(2)">Next → Parcel</button>
    </div>
</div>

{{-- ── Step 3: Parcel ──────────────────────────────────────────────── --}}
<div x-show="step === 3" x-cloak>
    <div class="step-card">
        <div class="step-title">📦 Step 3 — Parcel Details</div>
        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label required">Service Type</label>
                <select name="service_type" x-model="form.service_type" class="form-control">
                    <option value="express_air">✈️ Express Air (1-2 days)</option>
                    <option value="priority_surface">🚛 Priority Surface (3-5 days)</option>
                    <option value="economy_surface">📦 Economy Surface (5-7 days)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label required">Actual Weight (kg)</label>
                <input type="number" name="weight_actual" x-model="form.weight_actual" class="form-control" step="0.1" min="0.1" :class="{'is-invalid': errors.weight_actual}">
                <div class="inline-error" x-show="errors.weight_actual" x-text="errors.weight_actual" x-cloak></div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Parcel Type</label>
            <div class="radio-group">
                <label class="radio-card"><input type="radio" name="parcel_type" value="document" x-model="form.parcel_type"> 📄 Document</label>
                <label class="radio-card"><input type="radio" name="parcel_type" value="non_document" x-model="form.parcel_type"> 📦 Non-Document</label>
                <label class="radio-card"><input type="radio" name="parcel_type" value="fragile" x-model="form.parcel_type"> 🪟 Fragile</label>
            </div>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label class="form-label">Length (cm)</label>
                <input type="number" name="length" x-model="form.length" class="form-control" step="0.1" min="1" placeholder="L">
            </div>
            <div class="form-group">
                <label class="form-label">Width (cm)</label>
                <input type="number" name="width" x-model="form.width" class="form-control" step="0.1" min="1" placeholder="W">
            </div>
            <div class="form-group">
                <label class="form-label">Height (cm)</label>
                <input type="number" name="height" x-model="form.height" class="form-control" step="0.1" min="1" placeholder="H">
            </div>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label">Declared Value (₹)</label>
                <input type="number" name="declared_value" x-model="form.declared_value" class="form-control" step="0.01" min="0" placeholder="0.00">
            </div>
            <div class="form-group">
                <label class="form-label required">Number of Pieces</label>
                <input type="number" name="pieces" x-model="form.pieces" class="form-control" min="1" max="100">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Special Instructions</label>
            <textarea name="special_instructions" x-model="form.special_instructions" class="form-control" rows="2" placeholder="Handle with care, fragile items…" maxlength="500"></textarea>
        </div>
    </div>
    <div class="step-footer">
        <button type="button" class="btn btn-ghost" @click="prev(3)">← Receiver</button>
        <button type="button" class="btn btn-primary" @click="next(3)">Next → Pickup</button>
    </div>
</div>

{{-- ── Step 4: Pickup & Payment ────────────────────────────────────── --}}
<div x-show="step === 4" x-cloak>
    <div class="step-card">
        <div class="step-title">🚪 Step 4 — Pickup & Payment</div>

        <div class="form-group">
            <label class="form-label">Pickup Type</label>
            <div class="radio-group">
                <label class="radio-card"><input type="radio" name="pickup_type" value="door_pickup" x-model="form.pickup_type"> 🚪 Door Pickup</label>
                <label class="radio-card"><input type="radio" name="pickup_type" value="drop_at_office" x-model="form.pickup_type"> 🏢 Drop at Office</label>
            </div>
        </div>

        <div x-show="form.pickup_type === 'door_pickup'" x-cloak>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label required">Pickup Date</label>
                    <input type="date" name="pickup_date" x-model="form.pickup_date" class="form-control"
                           :min="new Date(Date.now()+86400000).toISOString().split('T')[0]">
                </div>
                <div class="form-group">
                    <label class="form-label">Preferred Time Slot</label>
                    <select name="pickup_slot" x-model="form.pickup_slot" class="form-control">
                        <option value="9-11am">9:00 AM – 11:00 AM</option>
                        <option value="11am-1pm">11:00 AM – 1:00 PM</option>
                        <option value="1-3pm">1:00 PM – 3:00 PM</option>
                        <option value="3-5pm">3:00 PM – 5:00 PM</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-top:8px">
            <label class="form-label">Payment Mode</label>
            <div class="radio-group">
                <label class="radio-card"><input type="radio" name="payment_mode" value="online" x-model="form.payment_mode"> 💳 Pay Online Now</label>
                @if($client?->account_type === 'credit')
                <label class="radio-card"><input type="radio" name="payment_mode" value="bill_to_account" x-model="form.payment_mode"> 📋 Bill to Account</label>
                @else
                <label class="radio-card"><input type="radio" name="payment_mode" value="bill_to_account" x-model="form.payment_mode"> 📋 Bill to Account</label>
                @endif
            </div>
            <p style="font-size:12px;color:#94a3b8;margin-top:6px">Credit account billing requires prior admin approval.</p>
        </div>
    </div>
    <div class="step-footer">
        <button type="button" class="btn btn-ghost" @click="prev(4)">← Parcel</button>
        <button type="button" class="btn btn-primary" @click="next(4)">Next → Review</button>
    </div>
</div>

{{-- ── Step 5: Review ──────────────────────────────────────────────── --}}
<div x-show="step === 5" x-cloak>
    <div class="step-card">
        <div class="step-title">🔍 Step 5 — Review & Confirm</div>

        <div class="review-grid">
            <div class="review-section">
                <div class="review-section-title">📤 Sender</div>
                <div class="review-row"><span class="review-label">Name:</span><span class="review-val" x-text="form.sender_name"></span></div>
                <div class="review-row"><span class="review-label">Phone:</span><span class="review-val" x-text="form.sender_phone"></span></div>
                <div class="review-row"><span class="review-label">Address:</span><span class="review-val" x-text="form.sender_address"></span></div>
                <div class="review-row"><span class="review-label">Pincode:</span><span class="review-val" x-text="form.sender_pincode"></span></div>
            </div>
            <div class="review-section">
                <div class="review-section-title">📥 Receiver</div>
                <div class="review-row"><span class="review-label">Name:</span><span class="review-val" x-text="form.receiver_name"></span></div>
                <div class="review-row"><span class="review-label">Phone:</span><span class="review-val" x-text="form.receiver_phone"></span></div>
                <div class="review-row"><span class="review-label">Address:</span><span class="review-val" x-text="form.receiver_address"></span></div>
                <div class="review-row"><span class="review-label">Pincode:</span><span class="review-val" x-text="form.receiver_pincode"></span></div>
            </div>
            <div class="review-section">
                <div class="review-section-title">📦 Parcel</div>
                <div class="review-row"><span class="review-label">Service:</span><span class="review-val" x-text="serviceLabel(form.service_type)"></span></div>
                <div class="review-row"><span class="review-label">Weight:</span><span class="review-val" x-text="form.weight_actual + ' kg'"></span></div>
                <div class="review-row"><span class="review-label">Type:</span><span class="review-val" x-text="form.parcel_type"></span></div>
                <div class="review-row"><span class="review-label">Pieces:</span><span class="review-val" x-text="form.pieces"></span></div>
            </div>
            <div class="review-section">
                <div class="review-section-title">🚪 Pickup</div>
                <div class="review-row"><span class="review-label">Type:</span><span class="review-val" x-text="form.pickup_type === 'door_pickup' ? 'Door Pickup' : 'Drop at Office'"></span></div>
                <div class="review-row" x-show="form.pickup_type === 'door_pickup'"><span class="review-label">Date:</span><span class="review-val" x-text="form.pickup_date"></span></div>
                <div class="review-row"><span class="review-label">Payment:</span><span class="review-val" x-text="form.payment_mode === 'online' ? 'Online' : 'Bill to Account'"></span></div>
            </div>
        </div>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 18px;font-size:13.5px;color:#15803d;margin-top:8px">
            ✅ Please review all details carefully. Once submitted, changes will require contacting support.
        </div>
    </div>
    <div class="step-footer">
        <button type="button" class="btn btn-ghost" @click="prev(5)">← Edit Pickup</button>
        <button type="submit" class="btn btn-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Confirm Booking
        </button>
    </div>
</div>

</form>
</div>
@endsection
