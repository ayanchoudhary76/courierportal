@extends('admin.layouts.app')
@section('page-title', 'Booking — ' . $booking->awb_number)

@push('styles')
<style>
.top-bar { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px; }
.awb-h { font-size:22px;font-weight:800;color:#0f172a;font-family:monospace; }
.badge { display:inline-flex;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700; }
.badge-delivered { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.badge-in_transit,.badge-picked_up,.badge-out_for_delivery { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
.badge-booked,.badge-pickup_scheduled { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
.badge-failed,.badge-returned { background:#fef2f2;color:#dc2626;border:1px solid #fecaca; }
.badge-default { background:#f8fafc;color:#64748b;border:1px solid #e2e8f0; }
.card { background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px;margin-bottom:16px; }
.card-title { font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #f1f5f9; }
.info-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px; }
@media(max-width:768px) { .info-grid { grid-template-columns:1fr; } }
.info-row { margin-bottom:10px; }
.info-label { font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:2px; }
.info-val { font-size:13.5px;font-weight:600;color:#1e293b; }
.charge-table { width:100%;border-collapse:collapse;font-size:13.5px; }
.charge-table td { padding:8px 0;border-bottom:1px solid #f1f5f9; }
.charge-table td:last-child { text-align:right;font-weight:600; }
.charge-table tr:last-child td { border-bottom:none;font-size:15px;font-weight:800;color:#1e40af;border-top:2px solid #e2e8f0;padding-top:10px; }
.fc { width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13.5px;color:#1e293b;outline:none;font-family:inherit;background:#f8fafc; }
.fc:focus { border-color:#2563eb; }
.fb { padding:10px 22px;background:#2563eb;color:#fff;border:none;border-radius:9px;font-size:13.5px;font-weight:600;cursor:pointer;font-family:inherit; }
.fb:hover { background:#1d4ed8; }
.fg { margin-bottom:12px; }
.fl { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px; }
/* Timeline */
.timeline { position:relative;padding-left:28px; }
.timeline::before { content:'';position:absolute;left:8px;top:0;bottom:0;width:2px;background:#e2e8f0; }
.tl-item { position:relative;padding-bottom:16px; }
.tl-item:last-child { padding-bottom:0; }
.tl-dot { position:absolute;left:-24px;top:2px;width:16px;height:16px;border-radius:50%;border:2px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center; }
.tl-dot.done { background:#2563eb;border-color:#2563eb; }
.tl-dot.current { background:#2563eb;border-color:#2563eb;animation:pulse 1.5s infinite; }
@keyframes pulse { 0%,100%{box-shadow:0 0 0 0 rgba(37,99,235,0.4);}50%{box-shadow:0 0 0 6px rgba(37,99,235,0);} }
.tl-label { font-size:13.5px;margin-bottom:2px; }
.tl-label.done,.tl-label.current { font-weight:700;color:#1e293b; }
.tl-label.future { color:#94a3b8; }
.tl-meta { font-size:12px;color:#64748b; }
.btn-label { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#1e40af;color:#fff;border-radius:9px;font-size:13.5px;font-weight:600;text-decoration:none; }
</style>
@endpush

@section('content')
<div style="max-width:1000px;margin:0 auto;padding:24px 20px 60px">

    <div class="top-bar">
        <div>
            <a href="{{ route('admin.bookings.index') }}" style="font-size:13px;color:#64748b;text-decoration:none">← All Bookings</a>
            <div class="awb-h" style="margin-top:4px">{{ $booking->awb_number }}</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            @php $sc = match(true) {
                $booking->booking_status==='delivered' => 'badge-delivered',
                in_array($booking->booking_status,['in_transit','picked_up','out_for_delivery']) => 'badge-in_transit',
                in_array($booking->booking_status,['booked','pickup_scheduled']) => 'badge-booked',
                in_array($booking->booking_status,['failed','returned']) => 'badge-failed',
                default => 'badge-default',
            }; @endphp
            <span class="badge {{ $sc }}" style="font-size:13px">{{ str_replace('_',' ',ucwords($booking->booking_status)) }}</span>
            <a href="{{ route('admin.bookings.label', $booking) }}" class="btn-label">📥 Print Label</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:12px 16px;color:#15803d;font-size:14px;font-weight:600;margin-bottom:14px">✅ {{ session('success') }}</div>
    @endif

    {{-- 3-col info --}}
    <div class="info-grid">
        <div class="card">
            <div class="card-title">📤 Sender</div>
            <div class="info-row"><div class="info-label">Name</div><div class="info-val">{{ $booking->sender_name }}</div></div>
            <div class="info-row"><div class="info-label">Address</div><div class="info-val">{{ $booking->sender_address }}</div></div>
            <div class="info-row"><div class="info-label">Pincode</div><div class="info-val" style="font-family:monospace">{{ $booking->sender_pincode }}</div></div>
            <div class="info-row"><div class="info-label">Phone</div><div class="info-val">{{ $booking->sender_phone }}</div></div>
        </div>
        <div class="card">
            <div class="card-title">📥 Receiver</div>
            <div class="info-row"><div class="info-label">Name</div><div class="info-val">{{ $booking->receiver_name }}</div></div>
            <div class="info-row"><div class="info-label">Address</div><div class="info-val">{{ $booking->receiver_address }}</div></div>
            <div class="info-row"><div class="info-label">Pincode</div><div class="info-val" style="font-family:monospace">{{ $booking->receiver_pincode }}</div></div>
            <div class="info-row"><div class="info-label">Phone</div><div class="info-val">{{ $booking->receiver_phone }}</div></div>
        </div>
        <div class="card">
            <div class="card-title">📦 Parcel</div>
            <div class="info-row"><div class="info-label">Service</div><div class="info-val">{{ str_replace('_',' ',ucwords($booking->service_type)) }}</div></div>
            <div class="info-row"><div class="info-label">Parcel Type</div><div class="info-val">{{ str_replace('_',' ',ucwords($booking->parcel_type)) }}</div></div>
            <div class="info-row"><div class="info-label">Weight</div><div class="info-val">{{ $booking->weight_actual }} kg</div></div>
            <div class="info-row"><div class="info-label">Pieces</div><div class="info-val">{{ $booking->pieces }}</div></div>
            <div class="info-row"><div class="info-label">Payment</div><div class="info-val">{{ str_replace('_',' ',ucwords($booking->payment_mode)) }} / {{ ucwords($booking->payment_status) }}</div></div>
            @if($booking->client)
            <div class="info-row">
                <div class="info-label">Client</div>
                <div class="info-val"><a href="{{ route('admin.clients.show', $booking->client) }}" style="color:#2563eb;text-decoration:none">{{ $booking->client->user?->name }}</a></div>
            </div>
            @endif
        </div>
    </div>

    {{-- Charge breakdown --}}
    <div class="card">
        <div class="card-title">💰 Charge Breakdown</div>
        <table class="charge-table">
            <tr><td>Base Freight</td><td>₹{{ number_format($booking->base_amount,2) }}</td></tr>
            @if($booking->surcharges)
            <tr><td>Fuel Surcharge ({{ $booking->surcharges['fuel_pct']??0 }}%)</td><td>₹{{ number_format($booking->surcharges['fuel_charge']??0,2) }}</td></tr>
            @if(($booking->surcharges['oda_charge']??0) > 0)
            <tr><td>ODA Charge</td><td>₹{{ number_format($booking->surcharges['oda_charge'],2) }}</td></tr>
            @endif
            <tr><td>GST (18%)</td><td>₹{{ number_format($booking->surcharges['gst']??0,2) }}</td></tr>
            @endif
            <tr><td>Total Amount</td><td>₹{{ number_format($booking->total_amount,2) }}</td></tr>
        </table>
    </div>

    {{-- Status Update --}}
    <div class="card">
        <div class="card-title">🔄 Update Status</div>
        <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                <div class="fg">
                    <label class="fl">New Status *</label>
                    <select name="status" class="fc" required>
                        @foreach(['booked','pickup_scheduled','picked_up','in_transit','out_for_delivery','delivered','failed','returned'] as $s)
                            <option value="{{ $s }}" {{ $booking->booking_status===$s?'selected':'' }}>{{ str_replace('_',' ',ucwords($s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label class="fl">Location</label>
                    <input type="text" name="tracking_location" class="fc" placeholder="e.g. Delhi Hub">
                </div>
                <div class="fg">
                    <label class="fl">Remarks</label>
                    <input type="text" name="tracking_remarks" class="fc" placeholder="Optional note">
                </div>
            </div>
            <button type="submit" class="fb" style="margin-top:8px">Update Status</button>
        </form>
    </div>

    {{-- Manual Tracking Event --}}
    <div class="card">
        <div class="card-title">➕ Add Manual Tracking Event</div>
        <form method="POST" action="{{ route('admin.bookings.tracking', $booking) }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px">
                <div class="fg">
                    <label class="fl">Event Type *</label>
                    <input type="text" name="event_type" class="fc" placeholder="e.g. in_transit" required>
                </div>
                <div class="fg">
                    <label class="fl">Location</label>
                    <input type="text" name="location" class="fc" placeholder="Hub name">
                </div>
                <div class="fg">
                    <label class="fl">Remarks</label>
                    <input type="text" name="remarks" class="fc" placeholder="Optional">
                </div>
                <div class="fg">
                    <label class="fl">Event Time *</label>
                    <input type="datetime-local" name="event_time" class="fc" required value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
            </div>
            <button type="submit" class="fb" style="margin-top:8px">Add Event</button>
        </form>
    </div>

    {{-- Timeline --}}
    <div class="card">
        <div class="card-title">📡 Tracking Timeline</div>
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
                    @if($isDone||$isCurrent)<svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="4"><polyline points="20 6 9 17 4 12"/></svg>@endif
                </div>
                <div class="tl-label {{ $state }}">{{ $label }}</div>
                @if($event)
                    <div class="tl-meta">{{ $event->event_time->format('d M Y, h:i A') }}@if($event->location) · {{ $event->location }}@endif @if($event->remarks)· {{ $event->remarks }}@endif <span style="color:#94a3b8">by {{ $event->creator?->name ?? 'System' }}</span></div>
                @elseif($isDone||$isCurrent)
                    <div class="tl-meta" style="color:#94a3b8">No event record</div>
                @endif
            </div>
        @endforeach
        </div>

        {{-- Non-standard events --}}
        @php $extraEvents = $booking->trackingEvents->whereNotIn('event_type', array_keys($statusOrder)); @endphp
        @if($extraEvents->count() > 0)
        <div style="margin-top:18px;padding-top:16px;border-top:1px solid #f1f5f9">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:10px">Additional Events</div>
            @foreach($extraEvents->sortBy('event_time') as $ev)
            <div class="tl-item" style="padding-left:0;padding-bottom:8px">
                <div style="font-size:13.5px;font-weight:600">📍 {{ str_replace('_',' ',ucwords($ev->event_type)) }}</div>
                <div class="tl-meta">{{ $ev->event_time->format('d M Y, h:i A') }}@if($ev->location) · {{ $ev->location }}@endif @if($ev->remarks)· {{ $ev->remarks }}@endif</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
