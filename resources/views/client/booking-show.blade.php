@extends('client.layouts.app')
@section('page-title', 'Booking — ' . $booking->awb_number)

@push('styles')
<style>
.page-wrap { max-width:960px; margin:0 auto; padding:28px 24px 60px; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:12px; }
.awb-title { font-size:22px; font-weight:800; color:#0f172a; font-family:monospace; }
.badge { display:inline-flex; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.badge-delivered  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-in_transit,.badge-picked_up,.badge-out_for_delivery { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.badge-booked,.badge-pickup_scheduled { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-failed { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.badge-default { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
.action-btns { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:22px; }
.btn { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px; font-size:13.5px; font-weight:600; text-decoration:none; transition:all 0.15s; }
.btn-primary { background:#2563eb; color:#fff; }
.btn-primary:hover { background:#1d4ed8; }
.btn-outline { border:1.5px solid #e2e8f0; color:#475569; background:#fff; }
.btn-outline:hover { border-color:#2563eb; color:#2563eb; }
.info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px; }
@media(max-width:768px) { .info-grid { grid-template-columns:1fr; } }
.info-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:20px; }
.info-card-title { font-size:11px; font-weight:700; color:#64748b; letter-spacing:0.07em; text-transform:uppercase; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid #f1f5f9; }
.info-row { margin-bottom:10px; }
.info-label { font-size:11px; color:#94a3b8; font-weight:600; margin-bottom:2px; }
.info-val { font-size:13.5px; font-weight:600; color:#1e293b; }
.charge-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:20px; margin-bottom:20px; }
.charge-title { font-size:11px; font-weight:700; color:#64748b; letter-spacing:0.07em; text-transform:uppercase; margin-bottom:14px; }
.charge-table { width:100%; border-collapse:collapse; font-size:13.5px; }
.charge-table td { padding:8px 0; border-bottom:1px solid #f1f5f9; }
.charge-table td:last-child { text-align:right; font-weight:600; }
.charge-table tr:last-child td { border-bottom:none; font-size:15px; font-weight:800; color:#1e40af; border-top:2px solid #e2e8f0; padding-top:10px; }
/* Timeline (same as tracking.blade.php) */
.tracking-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:20px; }
.timeline { position:relative; padding-left:28px; }
.timeline::before { content:''; position:absolute; left:8px; top:0; bottom:0; width:2px; background:#e2e8f0; }
.tl-item { position:relative; padding-bottom:18px; }
.tl-item:last-child { padding-bottom:0; }
.tl-dot { position:absolute; left:-24px; top:2px; width:16px; height:16px; border-radius:50%; border:2px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:center; }
.tl-dot.done { background:#2563eb; border-color:#2563eb; }
.tl-dot.current { background:#2563eb; border-color:#2563eb; animation:pulse 1.5s infinite; }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(37,99,235,0.4);} 50%{box-shadow:0 0 0 6px rgba(37,99,235,0);} }
.tl-label.done,.tl-label.current { font-weight:700; color:#1e293b; }
.tl-label.future { color:#94a3b8; font-weight:500; }
.tl-label { font-size:13.5px; margin-bottom:3px; }
.tl-meta { font-size:12px; color:#64748b; }
</style>
@endpush

@section('content')
<div class="page-wrap">

    <div class="page-header">
        <div>
            <div style="font-size:12px;color:#94a3b8;font-weight:600;margin-bottom:2px">BOOKING DETAIL</div>
            <div class="awb-title">{{ $booking->awb_number }}</div>
        </div>
        @php $sc = match(true) {
            $booking->booking_status === 'delivered' => 'badge-delivered',
            in_array($booking->booking_status,['in_transit','picked_up','out_for_delivery']) => 'badge-in_transit',
            in_array($booking->booking_status,['booked','pickup_scheduled']) => 'badge-booked',
            in_array($booking->booking_status,['failed','returned']) => 'badge-failed',
            default => 'badge-default',
        }; @endphp
        <span class="badge {{ $sc }}" style="font-size:13px;padding:6px 16px">{{ str_replace('_',' ', ucwords($booking->booking_status)) }}</span>
    </div>

    {{-- Action buttons --}}
    <div class="action-btns">
        <a href="{{ route('client.bookings.label', $booking->awb_number) }}" class="btn btn-primary">📥 Download AWB Label</a>
        <a href="{{ route('tracking.public', $booking->awb_number) }}" class="btn btn-outline">📡 Track Again</a>
        <a href="{{ route('client.book') }}?service_type={{ $booking->service_type }}&origin_pincode={{ $booking->sender_pincode }}&dest_pincode={{ $booking->receiver_pincode }}&weight={{ $booking->weight_actual }}" class="btn btn-outline">🔄 Re-book</a>
        <a href="{{ route('client.bookings') }}" class="btn btn-outline">← All Bookings</a>
    </div>

    {{-- Pay Now button (only for pending online payments) --}}
    @if($booking->payment_status === 'pending' && $booking->payment_mode === 'online')
    <div style="margin-bottom:22px;">
        <form method="GET" action="{{ route('client.payment.initiate') }}">
            <input type="hidden" name="awb_number" value="{{ $booking->awb_number }}">
            <button type="submit"
                style="display:inline-flex; align-items:center; gap:8px; padding:13px 28px;
                       background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; border:none;
                       border-radius:10px; font-size:15px; font-weight:700; cursor:pointer;
                       box-shadow:0 4px 14px rgba(22,163,74,0.35); transition:opacity 0.15s;">
                💳 Pay Now &mdash; &#8377;{{ number_format($booking->total_amount, 2) }}
            </button>
        </form>
    </div>
    @endif

    {{-- 3-column info --}}
    <div class="info-grid">
        <div class="info-card">
            <div class="info-card-title">📤 Sender</div>
            <div class="info-row"><div class="info-label">Name</div><div class="info-val">{{ $booking->sender_name }}</div></div>
            <div class="info-row"><div class="info-label">Address</div><div class="info-val">{{ $booking->sender_address }}</div></div>
            <div class="info-row"><div class="info-label">Pincode</div><div class="info-val" style="font-family:monospace">{{ $booking->sender_pincode }}</div></div>
            <div class="info-row"><div class="info-label">Phone</div><div class="info-val">{{ $booking->sender_phone }}</div></div>
        </div>
        <div class="info-card">
            <div class="info-card-title">📥 Receiver</div>
            <div class="info-row"><div class="info-label">Name</div><div class="info-val">{{ $booking->receiver_name }}</div></div>
            <div class="info-row"><div class="info-label">Address</div><div class="info-val">{{ $booking->receiver_address }}</div></div>
            <div class="info-row"><div class="info-label">Pincode</div><div class="info-val" style="font-family:monospace">{{ $booking->receiver_pincode }}</div></div>
            <div class="info-row"><div class="info-label">Phone</div><div class="info-val">{{ $booking->receiver_phone }}</div></div>
        </div>
        <div class="info-card">
            <div class="info-card-title">📦 Parcel & Service</div>
            <div class="info-row"><div class="info-label">Service</div><div class="info-val">{{ str_replace('_',' ', ucwords($booking->service_type)) }}</div></div>
            <div class="info-row"><div class="info-label">Parcel Type</div><div class="info-val">{{ str_replace('_',' ', ucwords($booking->parcel_type)) }}</div></div>
            <div class="info-row"><div class="info-label">Actual Weight</div><div class="info-val">{{ $booking->weight_actual }} kg</div></div>
            <div class="info-row"><div class="info-label">Pieces</div><div class="info-val">{{ $booking->pieces }}</div></div>
            <div class="info-row"><div class="info-label">Payment</div><div class="info-val">{{ str_replace('_',' ', ucwords($booking->payment_mode)) }}</div></div>
            @if($booking->special_instructions)
            <div class="info-row"><div class="info-label">Instructions</div><div class="info-val" style="font-size:12.5px">{{ $booking->special_instructions }}</div></div>
            @endif
        </div>
    </div>

    {{-- Charge breakdown --}}
    <div class="charge-card">
        <div class="charge-title">💰 Charge Breakdown</div>
        <table class="charge-table">
            <tr><td>Base Freight</td><td>₹{{ number_format($booking->base_amount, 2) }}</td></tr>
            @if($booking->surcharges)
            <tr><td>Fuel Surcharge ({{ $booking->surcharges['fuel_pct'] ?? 0 }}%)</td><td>₹{{ number_format($booking->surcharges['fuel_charge'] ?? 0, 2) }}</td></tr>
            @if(($booking->surcharges['oda_charge'] ?? 0) > 0)
            <tr><td>ODA Charge</td><td>₹{{ number_format($booking->surcharges['oda_charge'], 2) }}</td></tr>
            @endif
            <tr><td>GST (18%)</td><td>₹{{ number_format($booking->surcharges['gst'] ?? 0, 2) }}</td></tr>
            @endif
            <tr><td>Total Amount</td><td>₹{{ number_format($booking->total_amount, 2) }}</td></tr>
        </table>
    </div>

    {{-- Tracking timeline --}}
    <div class="tracking-card">
        <div class="charge-title" style="margin-bottom:16px">📡 Tracking Timeline</div>
        @php
            $statusOrder = ['booked'=>'📝 Booking Confirmed','pickup_scheduled'=>'📅 Pickup Scheduled','picked_up'=>'🚚 Picked Up','in_transit'=>'🔄 In Transit','out_for_delivery'=>'🛵 Out for Delivery','delivered'=>'✅ Delivered'];
            $currentIdx  = array_search($booking->booking_status, array_keys($statusOrder));
            $eventsByType= $booking->trackingEvents->keyBy('event_type');
        @endphp
        <div class="timeline">
        @foreach($statusOrder as $status => $label)
            @php
                $idx      = array_search($status, array_keys($statusOrder));
                $isCurrent= $status === $booking->booking_status;
                $isDone   = $currentIdx !== false && $idx < $currentIdx;
                $event    = $eventsByType->get($status);
                $state    = $isDone ? 'done' : ($isCurrent ? 'current' : 'future');
            @endphp
            <div class="tl-item">
                <div class="tl-dot {{ $state }}">
                    @if($isDone || $isCurrent)
                        <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                    @endif
                </div>
                <div class="tl-label {{ $state }}">{{ $label }}</div>
                @if($event)
                    <div class="tl-meta">{{ $event->event_time->format('d M Y, h:i A') }}@if($event->location) &bull; {{ $event->location }}@endif</div>
                @elseif(!$isDone && !$isCurrent)
                    <div class="tl-meta" style="color:#d1d5db">Pending</div>
                @endif
            </div>
        @endforeach
        </div>
    </div>
</div>
@endsection
