@extends('client.layouts.app')
@section('page-title', 'Track Shipment')

@push('styles')
<style>
.page-wrap { max-width:820px; margin:0 auto; padding:32px 24px 60px; }
.search-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; padding:32px; text-align:center; box-shadow:0 2px 12px rgba(0,0,0,0.05); margin-bottom:28px; }
.search-title { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:6px; }
.search-sub { font-size:14px; color:#64748b; margin-bottom:22px; }
.search-row { display:flex; gap:10px; max-width:500px; margin:0 auto; }
.search-input { flex:1; padding:12px 16px; border:2px solid #e2e8f0; border-radius:10px; font-size:14px; font-family:monospace; outline:none; transition:border-color 0.2s; }
.search-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.search-btn { padding:12px 24px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; }
.search-btn:hover { background:#1d4ed8; }

/* Booking card */
.booking-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:24px; box-shadow:0 1px 4px rgba(0,0,0,0.04); margin-bottom:20px; }
.awb-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px; }
.awb-num { font-family:monospace; font-size:20px; font-weight:800; color:#1e293b; }
.badge { display:inline-flex; align-items:center; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.badge-delivered  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-in_transit { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.badge-booked     { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-failed     { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.badge-default    { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
.info-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; border-top:1px solid #f1f5f9; padding-top:16px; }
@media(max-width:640px) { .info-grid { grid-template-columns:repeat(2,1fr); } }
.info-item .label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.04em; margin-bottom:3px; }
.info-item .val { font-size:13.5px; font-weight:600; color:#1e293b; }

/* Timeline */
.timeline-wrap { padding-top:16px; border-top:1px solid #f1f5f9; }
.timeline-title { font-size:13px; font-weight:700; color:#64748b; letter-spacing:0.05em; text-transform:uppercase; margin-bottom:18px; }
.timeline { position:relative; padding-left:28px; }
.timeline::before { content:''; position:absolute; left:8px; top:0; bottom:0; width:2px; background:#e2e8f0; }
.tl-item { position:relative; padding-bottom:20px; }
.tl-item:last-child { padding-bottom:0; }
.tl-dot { position:absolute; left:-24px; top:2px; width:16px; height:16px; border-radius:50%; border:2px solid #e2e8f0; background:#fff; display:flex; align-items:center; justify-content:center; }
.tl-dot.done { background:#2563eb; border-color:#2563eb; }
.tl-dot.current { background:#2563eb; border-color:#2563eb; animation:pulse 1.5s infinite; }
.tl-dot.future { background:#fff; border-color:#d1d5db; }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(37,99,235,0.4);} 50%{box-shadow:0 0 0 6px rgba(37,99,235,0);} }
.tl-inner.done .tl-label { color:#1e293b; font-weight:700; }
.tl-inner.current .tl-label { color:#2563eb; font-weight:800; }
.tl-inner.future .tl-label { color:#94a3b8; font-weight:500; }
.tl-label { font-size:14px; margin-bottom:3px; }
.tl-meta { font-size:12px; color:#64748b; }

.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:14px 18px; color:#15803d; font-size:14px; font-weight:600; margin-bottom:20px; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:14px 18px; color:#dc2626; font-size:14px; font-weight:600; margin-bottom:20px; }
.alert-info { background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 18px; color:#1d4ed8; font-size:14px; margin-bottom:20px; }
</style>
@endpush

@section('content')
<div class="page-wrap">

    {{-- Search box --}}
    <div class="search-card">
        <div class="search-title">📡 Track Your Shipment</div>
        <p class="search-sub">Enter your AWB number to get real-time status updates</p>
        <form method="GET" action="{{ route('tracking.public') }}">
            <div class="search-row">
                <input type="text" name="awb" value="{{ $awb }}"
                       class="search-input" placeholder="e.g. CP2505001234"
                       style="text-transform:uppercase">
                <button type="submit" class="search-btn">🔍 Track</button>
            </div>
        </form>
    </div>

    {{-- No AWB provided --}}
    @if(!$awb)
        <div class="alert-info">
            📦 Enter an AWB / tracking number above to see your shipment status.
        </div>
    @elseif(!$booking)
        <div class="alert-error">
            ❌ No shipment found with AWB: <strong>{{ strtoupper($awb) }}</strong>. Please check the number and try again.
        </div>
    @else

    {{-- Success / Failed banners --}}
    @if($booking->booking_status === 'delivered')
        <div class="alert-success">✅ Shipment <strong>{{ $booking->awb_number }}</strong> has been successfully delivered!</div>
    @elseif($booking->booking_status === 'failed')
        <div class="alert-error">⚠️ Delivery attempt failed for AWB <strong>{{ $booking->awb_number }}</strong>. Please contact support.</div>
    @endif

    {{-- Booking info card --}}
    <div class="booking-card">
        <div class="awb-header">
            <div>
                <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:2px">AWB Number</div>
                <div class="awb-num">{{ $booking->awb_number }}</div>
            </div>
            @php
                $sc = match($booking->booking_status) {
                    'delivered'                  => 'badge-delivered',
                    'in_transit','picked_up',
                    'out_for_delivery'           => 'badge-in_transit',
                    'booked','pickup_scheduled'  => 'badge-booked',
                    'failed','returned'          => 'badge-failed',
                    default                      => 'badge-default',
                };
            @endphp
            <span class="badge {{ $sc }}">{{ str_replace('_',' ', ucwords($booking->booking_status)) }}</span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="label">From</div>
                <div class="val">{{ $booking->sender_pincode }}</div>
            </div>
            <div class="info-item">
                <div class="label">To</div>
                <div class="val">{{ $booking->receiver_pincode }}</div>
            </div>
            <div class="info-item">
                <div class="label">Service</div>
                <div class="val">{{ str_replace('_',' ', ucwords($booking->service_type)) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Weight</div>
                <div class="val">{{ $booking->weight_actual }} kg</div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="timeline-wrap">
            <div class="timeline-title">Shipment Journey</div>
            <div class="timeline">
                @php
                    $statusOrder = [
                        'booked'             => ['label'=>'Booking Confirmed',    'icon'=>'📝'],
                        'pickup_scheduled'   => ['label'=>'Pickup Scheduled',     'icon'=>'📅'],
                        'picked_up'          => ['label'=>'Picked Up',            'icon'=>'🚚'],
                        'in_transit'         => ['label'=>'In Transit',           'icon'=>'🔄'],
                        'out_for_delivery'   => ['label'=>'Out for Delivery',     'icon'=>'🛵'],
                        'delivered'          => ['label'=>'Delivered',            'icon'=>'✅'],
                    ];
                    $currentStatusIdx = array_search($booking->booking_status, array_keys($statusOrder));
                    $eventsByType = $events->keyBy('event_type');
                @endphp

                @foreach($statusOrder as $status => $info)
                    @php
                        $idx      = array_search($status, array_keys($statusOrder));
                        $isCurrent= $status === $booking->booking_status;
                        $isDone   = $currentStatusIdx !== false && $idx < $currentStatusIdx;
                        $isFuture = !$isCurrent && !$isDone;
                        $event    = $eventsByType->get($status);
                        $state    = $isDone ? 'done' : ($isCurrent ? 'current' : 'future');
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot {{ $state }}">
                            @if($isDone || $isCurrent)
                                <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                        </div>
                        <div class="tl-inner {{ $state }}">
                            <div class="tl-label">{{ $info['icon'] }} {{ $info['label'] }}</div>
                            @if($event)
                                <div class="tl-meta">
                                    {{ $event->event_time->format('d M Y, h:i A') }}
                                    @if($event->location) &bull; {{ $event->location }} @endif
                                    @if($event->remarks) &bull; {{ $event->remarks }} @endif
                                </div>
                            @elseif($isFuture)
                                <div class="tl-meta" style="color:#d1d5db">Pending</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Estimated delivery note --}}
        <div style="background:#f8fafc;border-radius:9px;padding:12px 16px;margin-top:18px;font-size:13px;color:#475569">
            🕐 <strong>Estimated Transit:</strong>
            {{ match($booking->service_type) {
                'express_air'      => '1-2 business days',
                'priority_surface' => '3-5 business days',
                'economy_surface'  => '5-7 business days',
                default            => '3-7 business days',
            } }}
            from booking date ({{ $booking->created_at->format('d M Y') }})
        </div>
    </div>

    @auth
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('client.bookings.show', $booking->awb_number) }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#2563eb;color:#fff;border-radius:9px;font-weight:600;text-decoration:none;font-size:13.5px">
           📋 Full Booking Details
        </a>
        <a href="{{ route('client.bookings.label', $booking->awb_number) }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:9px;font-weight:600;text-decoration:none;font-size:13.5px">
           📥 Download Label
        </a>
    </div>
    @endauth

    @endif
</div>
@endsection
