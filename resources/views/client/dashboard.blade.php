@extends('client.layouts.app')
@section('page-title', 'My Dashboard')

@push('styles')
<style>
.dash-wrap { max-width:1100px; margin:0 auto; padding:32px 24px 60px; }
.welcome-bar { background:linear-gradient(135deg,#1e40af,#4f46e5); border-radius:16px; padding:28px 32px; color:#fff; display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:14px; }
.welcome-title { font-size:20px; font-weight:800; margin-bottom:4px; }
.welcome-sub { font-size:13px; color:rgba(255,255,255,0.7); }
.welcome-id { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); border-radius:8px; padding:6px 14px; font-size:12.5px; font-weight:700; letter-spacing:0.05em; }

.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
@media(max-width:900px) { .stats-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px) { .stats-grid { grid-template-columns:1fr; } }
.stat-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; padding:20px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 3px rgba(0,0,0,0.04); border-left:4px solid transparent; transition:box-shadow 0.2s; }
.stat-card:hover { box-shadow:0 6px 20px rgba(0,0,0,0.08); }
.stat-card.blue   { border-left-color:#3b82f6; }
.stat-card.yellow { border-left-color:#f59e0b; }
.stat-card.green  { border-left-color:#10b981; }
.stat-card.red    { border-left-color:#ef4444; }
.stat-card.gray   { border-left-color:#94a3b8; }
.stat-icon { font-size:28px; flex-shrink:0; }
.stat-val { font-size:26px; font-weight:800; color:#0f172a; line-height:1; }
.stat-lbl { font-size:12px; color:#64748b; margin-top:4px; }

.quick-actions { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:28px; }
.qa-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:10px; font-size:13.5px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; color:#475569; background:#fff; transition:all 0.15s; }
.qa-btn:hover { border-color:#2563eb; color:#2563eb; background:#eff6ff; }
.qa-btn.primary { background:#2563eb; color:#fff; border-color:#2563eb; }
.qa-btn.primary:hover { background:#1d4ed8; color:#fff; }

.panel { background:#fff; border-radius:14px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
.panel-header { padding:16px 20px; border-bottom:1px solid #f1f5f9; font-size:14px; font-weight:700; color:#1e293b; display:flex; align-items:center; justify-content:space-between; }
.panel-header a { font-size:12.5px; color:#2563eb; text-decoration:none; font-weight:500; }
table { width:100%; border-collapse:collapse; }
thead th { background:#f8fafc; padding:11px 16px; text-align:left; font-size:11.5px; font-weight:700; color:#64748b; letter-spacing:0.05em; text-transform:uppercase; border-bottom:1px solid #e2e8f0; }
tbody td { padding:13px 16px; font-size:13.5px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover { background:#fafcff; }
.badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
.badge-green  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.badge-yellow { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-red    { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.badge-gray   { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
.empty-state { padding:60px; text-align:center; color:#94a3b8; }
.empty-icon { font-size:40px; margin-bottom:14px; }
.empty-title { font-size:16px; font-weight:600; color:#475569; margin-bottom:8px; }
.action-link { font-size:12px; color:#2563eb; text-decoration:none; font-weight:500; }
.action-link:hover { text-decoration:underline; }
</style>
@endpush

@section('content')
<div class="dash-wrap">

    {{-- Welcome bar --}}
    <div class="welcome-bar">
        <div>
            <div class="welcome-title">Welcome back, {{ Auth::user()->name }}! 👋</div>
            <div class="welcome-sub">
                {{ $client?->company_name }} &bull; {{ $client ? ucfirst($client->account_type) . ' Account' : '' }}
                &bull; Rate Card: {{ $client?->rateCard?->name ?? 'Standard' }}
            </div>
        </div>
        @if($client)
        <div class="welcome-id">Client ID: C-{{ str_pad($client->id, 4, '0', STR_PAD_LEFT) }}</div>
        @endif
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">📦</div>
            <div><div class="stat-val">{{ $totalBookings }}</div><div class="stat-lbl">Total Bookings</div></div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-icon">🚛</div>
            <div><div class="stat-val">{{ $activeShipments }}</div><div class="stat-lbl">Active Shipments</div></div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">✅</div>
            <div><div class="stat-val">{{ $deliveredCount }}</div><div class="stat-lbl">Delivered</div></div>
        </div>
        <div class="stat-card {{ $outstandingBalance > 0 ? 'red' : 'gray' }}">
            <div class="stat-icon">💰</div>
            <div>
                <div class="stat-val" style="font-size:20px;{{ $outstandingBalance > 0 ? 'color:#dc2626' : '' }}">₹{{ number_format($outstandingBalance, 2) }}</div>
                <div class="stat-lbl">Outstanding Balance</div>
            </div>
        </div>
    </div>

    {{-- Outstanding balance warning --}}
    @if($outstandingBalance > 0)
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 18px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <span style="font-size:14px;color:#92400e;font-weight:600">
            ⚠️ You have <strong>₹{{ number_format($outstandingBalance, 2) }}</strong> outstanding balance.
            View your bookings to settle payment.
        </span>
        <a href="{{ route('client.bookings', ['status'=>'booked']) }}" style="font-size:13px;color:#b45309;font-weight:700;text-decoration:none;border:1px solid #fde68a;border-radius:7px;padding:5px 12px">View Bookings →</a>
    </div>
    @endif

    {{-- Quick actions --}}
    <div class="quick-actions">
        <a href="{{ route('client.book') }}" class="qa-btn primary">📦 Book New Shipment</a>
        <a href="{{ route('tracking.public') }}" class="qa-btn">📡 Track Shipment</a>
        <a href="{{ route('client.rates') }}" class="qa-btn">💰 Rate Calculator</a>
        <a href="{{ route('client.bookings') }}" class="qa-btn">📋 All Bookings</a>
        <a href="{{ route('client.tickets') }}" class="qa-btn">🎫 Support</a>
        <a href="{{ route('client.profile') }}" class="qa-btn">👤 My Profile</a>
    </div>

    {{-- Recent bookings --}}
    <div class="panel">
        <div class="panel-header">
            <span>📋 Recent Bookings</span>
            <a href="{{ route('client.bookings') }}">View all →</a>
        </div>
        @if($recentBookings->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <div class="empty-title">No bookings yet</div>
                <p style="font-size:13.5px;margin-bottom:18px">Book your first shipment and it will appear here.</p>
                <a href="{{ route('client.book') }}" style="display:inline-flex;align-items:center;gap:7px;padding:10px 22px;background:#2563eb;color:#fff;border-radius:9px;font-weight:600;text-decoration:none;font-size:14px">📦 Book Now</a>
            </div>
        @else
        <table>
            <thead><tr>
                <th>AWB No.</th><th>Destination</th><th>Status</th><th>Amount</th><th>Date</th><th>Actions</th>
            </tr></thead>
            <tbody>
            @foreach($recentBookings as $b)
            <tr>
                <td style="font-family:monospace;font-weight:700;color:#2563eb">{{ $b->awb_number }}</td>
                <td>{{ $b->receiver_name }}<br><span style="font-size:11.5px;color:#94a3b8">{{ $b->receiver_pincode }}</span></td>
                <td>
                    @php $sc = match($b->booking_status) {
                        'delivered' => 'badge-green',
                        'in_transit','picked_up','out_for_delivery' => 'badge-blue',
                        'failed','returned' => 'badge-red',
                        default => 'badge-yellow',
                    }; @endphp
                    <span class="badge {{ $sc }}">{{ str_replace('_',' ',ucwords($b->booking_status)) }}</span>
                </td>
                <td style="font-weight:600">₹{{ number_format($b->total_amount,2) }}</td>
                <td style="color:#94a3b8;font-size:12.5px">{{ $b->created_at->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('tracking.public', $b->awb_number) }}" class="action-link">Track</a> &bull;
                    <a href="{{ route('client.bookings.label', $b->awb_number) }}" class="action-link">Label</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
