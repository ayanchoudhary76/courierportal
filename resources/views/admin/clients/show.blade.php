@extends('admin.layouts.app')

@section('page-title')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
    </svg>
    {{ $client->company_name }}
@endsection

@push('styles')
<style>
    .page-actions { display:flex; gap:10px; justify-content:flex-end; margin-bottom:20px; flex-wrap:wrap; }
    .btn {
        display:inline-flex; align-items:center; gap:7px;
        padding:9px 18px; border-radius:9px; font-size:13.5px;
        font-weight:600; cursor:pointer; border:none;
        text-decoration:none; transition:opacity 0.15s;
    }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { opacity:0.88; }
    .btn-outline { background:#fff; color:#475569; border:1.5px solid #e2e8f0; }
    .btn-outline:hover { background:#f8fafc; }

    .info-grid { display:grid; grid-template-columns:2fr 1fr; gap:20px; margin-bottom:20px; }
    @media(max-width:900px) { .info-grid { grid-template-columns:1fr; } }

    .card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
    .card-header {
        padding:16px 20px; border-bottom:1px solid #f1f5f9;
        font-size:13px; font-weight:700; color:#64748b;
        letter-spacing:0.05em; text-transform:uppercase;
        display:flex; align-items:center; gap:7px;
    }
    .card-body { padding:20px; }

    .info-row { display:flex; justify-content:space-between; align-items:baseline; padding:8px 0; border-bottom:1px solid #f8fafc; }
    .info-row:last-child { border-bottom:none; }
    .info-label { font-size:12.5px; color:#94a3b8; font-weight:500; }
    .info-value { font-size:13.5px; color:#1e293b; font-weight:600; text-align:right; }

    .stat-cards { display:grid; grid-template-columns:1fr; gap:12px; }
    .stat-box {
        background:#fff; border-radius:12px; border:1px solid #e2e8f0;
        padding:16px 18px; display:flex; justify-content:space-between; align-items:center;
    }
    .stat-box-val { font-size:22px; font-weight:800; color:#0f172a; }
    .stat-box-label { font-size:12px; color:#64748b; margin-top:3px; }
    .stat-box-icon { font-size:26px; }

    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .badge-blue    { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .badge-orange  { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
    .badge-green   { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-red     { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .badge-purple  { background:#faf5ff; color:#7c3aed; border:1px solid #ddd6fe; }
    .badge-yellow  { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
    .badge-gray    { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

    table { width:100%; border-collapse:collapse; }
    thead th { background:#f8fafc; padding:11px 16px; text-align:left; font-size:11.5px; font-weight:700; color:#64748b; letter-spacing:0.05em; text-transform:uppercase; border-bottom:1px solid #e2e8f0; }
    tbody td { padding:12px 16px; font-size:13.5px; color:#1e293b; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafcff; }

    .section-gap { margin-top:20px; }
    .empty-msg { padding:30px; text-align:center; color:#94a3b8; font-size:13.5px; }
</style>
@endpush

@section('content')

{{-- Action buttons --}}
<div class="page-actions">
    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline">← All Clients</a>
    <a href="{{ route('admin.clients.bookings', $client->id) }}" class="btn btn-outline">📦 All Bookings</a>
    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-primary">✏️ Edit Client</a>
</div>

{{-- Info + stats grid --}}
<div class="info-grid">
    {{-- Left: Client details --}}
    <div class="card">
        <div class="card-header">🏢 Client Details</div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label">Company</span>
                <span class="info-value">{{ $client->company_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Contact Person</span>
                <span class="info-value">{{ $client->user?->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value">{{ $client->user?->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone</span>
                <span class="info-value">{{ $client->user?->phone ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">GSTIN</span>
                <span class="info-value" style="font-family:monospace">{{ $client->gstin ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Address</span>
                <span class="info-value">{{ $client->address }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">City / State</span>
                <span class="info-value">{{ $client->city }}, {{ $client->state }} — {{ $client->pincode }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Account Type</span>
                <span class="info-value">
                    @if($client->account_type === 'credit')
                        <span class="badge badge-orange">Credit — ₹{{ number_format($client->credit_limit, 2) }} limit</span>
                    @else
                        <span class="badge badge-blue">Prepaid</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Rate Card</span>
                <span class="info-value">{{ $client->rateCard?->name ?? '—' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if($client->is_active)
                        <span class="badge badge-green">● Active</span>
                    @else
                        <span class="badge badge-red">● Suspended</span>
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Member Since</span>
                <span class="info-value">{{ $client->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Right: Stats --}}
    <div class="stat-cards">
        <div class="stat-box">
            <div>
                <div class="stat-box-val">{{ $totalBookings }}</div>
                <div class="stat-box-label">Total Bookings</div>
            </div>
            <div class="stat-box-icon">📦</div>
        </div>
        <div class="stat-box">
            <div>
                <div class="stat-box-val">₹{{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-box-label">Total Revenue</div>
            </div>
            <div class="stat-box-icon">💰</div>
        </div>
        <div class="stat-box">
            <div>
                <div class="stat-box-val" style="color:{{ $outstandingBalance > 0 ? '#dc2626' : '#15803d' }}">
                    ₹{{ number_format($outstandingBalance, 2) }}
                </div>
                <div class="stat-box-label">Outstanding Balance</div>
            </div>
            <div class="stat-box-icon">⚠️</div>
        </div>
    </div>
</div>

{{-- Recent Bookings --}}
<div class="card section-gap">
    <div class="card-header">📦 Recent Bookings (Last 10)</div>
    @if($client->bookings->isEmpty())
        <div class="empty-msg">No bookings yet for this client.</div>
    @else
    <table>
        <thead><tr>
            <th>AWB No.</th>
            <th>Destination</th>
            <th>Service</th>
            <th>Status</th>
            <th>Amount</th>
            <th>Date</th>
        </tr></thead>
        <tbody>
        @foreach($client->bookings as $booking)
        <tr>
            <td style="font-family:monospace;font-weight:700;color:#3b82f6">{{ $booking->awb_number }}</td>
            <td>{{ $booking->receiver_name }}<br><span style="font-size:12px;color:#64748b">{{ $booking->receiver_pincode }}</span></td>
            <td><span class="badge badge-gray">{{ str_replace('_',' ',ucfirst($booking->service_type)) }}</span></td>
            <td>
                @php
                $sc = match($booking->booking_status) {
                    'delivered' => 'badge-green',
                    'in_transit','picked_up' => 'badge-blue',
                    'failed','returned' => 'badge-red',
                    'booked','pickup_scheduled' => 'badge-yellow',
                    default => 'badge-gray',
                };
                @endphp
                <span class="badge {{ $sc }}">{{ str_replace('_',' ',ucwords($booking->booking_status)) }}</span>
            </td>
            <td style="font-weight:600">₹{{ number_format($booking->total_amount, 2) }}</td>
            <td style="color:#64748b;font-size:12.5px">{{ $booking->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Recent Tickets --}}
<div class="card section-gap">
    <div class="card-header">🎫 Recent Support Tickets (Last 5)</div>
    @if($client->supportTickets->isEmpty())
        <div class="empty-msg">No support tickets raised.</div>
    @else
    <table>
        <thead><tr>
            <th>Ticket #</th>
            <th>Category</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Date</th>
        </tr></thead>
        <tbody>
        @foreach($client->supportTickets as $ticket)
        <tr>
            <td style="font-family:monospace;font-weight:700;color:#7c3aed">{{ $ticket->ticket_number }}</td>
            <td><span class="badge badge-gray">{{ ucfirst(str_replace('_',' ',$ticket->category)) }}</span></td>
            <td>{{ Str::limit($ticket->subject, 45) }}</td>
            <td>
                @php
                $ts = match($ticket->status) {
                    'open'       => 'badge-red',
                    'inprogress' => 'badge-yellow',
                    'resolved'   => 'badge-green',
                    'closed'     => 'badge-gray',
                    default      => 'badge-gray',
                };
                @endphp
                <span class="badge {{ $ts }}">{{ ucfirst($ticket->status) }}</span>
            </td>
            <td style="color:#64748b;font-size:12.5px">{{ $ticket->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
