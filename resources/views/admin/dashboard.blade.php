@extends('admin.layouts.app')

@section('page-title')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6">
        <rect x="3" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/>
    </svg>
    Dashboard
@endsection

@push('styles')
<style>
    /* ── Welcome Banner ────────────────────────────────────────── */
    .welcome-card {
        background: linear-gradient(135deg, #1e40af 0%, #4f46e5 100%);
        border-radius: 16px;
        padding: 24px 30px;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 22px;
        overflow: hidden;
        position: relative;
    }
    .welcome-card::before {
        content: '';
        position: absolute; right: -50px; top: -50px;
        width: 220px; height: 220px;
        background: rgba(255,255,255,0.06); border-radius: 50%;
    }
    .welcome-card::after {
        content: '';
        position: absolute; right: 80px; bottom: -70px;
        width: 170px; height: 170px;
        background: rgba(255,255,255,0.04); border-radius: 50%;
    }
    .welcome-title { font-size: 19px; font-weight: 700; margin-bottom: 5px; }
    .welcome-sub { font-size: 13px; color: rgba(255,255,255,0.72); }
    .welcome-emoji { font-size: 48px; position: relative; z-index: 1; }

    /* ── Stat Grid ─────────────────────────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 22px;
    }
    @media(max-width:1100px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    @media(max-width:600px)  { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: box-shadow 0.2s, transform 0.15s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border-left: 4px solid transparent;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.08); transform: translateY(-2px); }

    .stat-card.blue   { border-left-color: #3b82f6; }
    .stat-card.yellow { border-left-color: #f59e0b; }
    .stat-card.purple { border-left-color: #8b5cf6; }
    .stat-card.green  { border-left-color: #10b981; }
    .stat-card.red    { border-left-color: #ef4444; }
    .stat-card.teal   { border-left-color: #14b8a6; }
    .stat-card.indigo { border-left-color: #6366f1; }
    .stat-card.orange { border-left-color: #f97316; }

    .stat-icon {
        width: 46px; height: 46px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 21px; flex-shrink: 0;
    }
    .stat-card.blue   .stat-icon { background: #eff6ff; }
    .stat-card.yellow .stat-icon { background: #fffbeb; }
    .stat-card.purple .stat-icon { background: #faf5ff; }
    .stat-card.green  .stat-icon { background: #f0fdf4; }
    .stat-card.red    .stat-icon { background: #fef2f2; }
    .stat-card.teal   .stat-icon { background: #f0fdfa; }
    .stat-card.indigo .stat-icon { background: #eef2ff; }
    .stat-card.orange .stat-icon { background: #fff7ed; }

    .stat-value { font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; line-height: 1; }
    .stat-label { font-size: 12px; color: #64748b; font-weight: 500; margin-top: 4px; }

    /* ── Recent Bookings Table ──────────────────────────────────── */
    .table-card {
        background: #fff; border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .table-header {
        padding: 16px 20px; border-bottom: 1px solid #f1f5f9;
        font-size: 14px; font-weight: 700; color: #1e293b;
        display: flex; align-items: center; justify-content: space-between;
    }
    .table-header a { font-size: 12.5px; color: #3b82f6; text-decoration: none; font-weight: 500; }
    .table-header a:hover { text-decoration: underline; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #f8fafc; padding: 11px 16px; text-align: left; font-size: 11.5px; font-weight: 700; color: #64748b; letter-spacing: 0.05em; text-transform: uppercase; border-bottom: 1px solid #e2e8f0; }
    tbody td { padding: 13px 16px; font-size: 13.5px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafcff; }

    .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .badge-green  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-blue   { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .badge-red    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-yellow { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .badge-purple { background: #faf5ff; color: #7c3aed; border: 1px solid #ddd6fe; }
    .badge-gray   { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    .empty-state { padding: 50px; text-align: center; color: #94a3b8; }
    .empty-state-icon { font-size: 38px; margin-bottom: 10px; }
    .empty-state-title { font-size: 14px; font-weight: 600; color: #475569; }
</style>
@endpush

@section('content')

{{-- Welcome banner --}}
<div class="welcome-card">
    <div>
        <div class="welcome-title">
            Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, {{ Auth::user()->name }}! 👋
        </div>
        <div class="welcome-sub">Here's your operations overview for {{ now()->format('l, d M Y') }}</div>
    </div>
    <div class="welcome-emoji">🚚</div>
</div>

{{-- ── 8 Stat Cards (single flat grid, align-items:start prevents stretching) ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;align-items:start;margin-bottom:24px;">

    {{-- 1: Today's Bookings --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #3b82f6;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">📦</div>
        <div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">{{ $stats['todayBookings'] }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">Today's Bookings</div>
        </div>
    </div>

    {{-- 2: Pending Pickups --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #f59e0b;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#fffbeb;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🔔</div>
        <div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">{{ $stats['pendingPickups'] }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">Pending Pickups</div>
        </div>
    </div>

    {{-- 3: In Transit --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #8b5cf6;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#faf5ff;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🚛</div>
        <div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">{{ $stats['inTransit'] }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">In Transit</div>
        </div>
    </div>

    {{-- 4: Delivered Today --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #10b981;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">✅</div>
        <div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">{{ $stats['deliveredToday'] }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">Delivered Today</div>
        </div>
    </div>

    {{-- 5: Open Tickets --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #ef4444;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🎫</div>
        <div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">{{ $stats['openTickets'] }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">Open Tickets</div>
        </div>
    </div>

    {{-- 6: Revenue Today --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #14b8a6;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">💰</div>
        <div>
            <div style="font-size:20px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">₹{{ number_format($stats['revenueToday'], 2) }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">Revenue Today</div>
        </div>
    </div>

    {{-- 7: New Clients This Week --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #6366f1;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#eef2ff;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">👥</div>
        <div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">{{ $stats['newClientsWeek'] }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">New Clients (Week)</div>
        </div>
    </div>

    {{-- 8: Failed Deliveries --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;border-left:4px solid #f97316;padding:20px 22px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
        <div style="width:46px;height:46px;border-radius:12px;background:#fff7ed;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">❌</div>
        <div>
            <div style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;letter-spacing:-0.02em;">{{ $stats['failedDeliveries'] }}</div>
            <div style="font-size:12.5px;color:#64748b;margin-top:4px;font-weight:500;">Failed Deliveries</div>
        </div>
    </div>

</div>{{-- /stats grid --}}

{{-- ── Quick Actions ──────────────────────────────────────────────────── --}}
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:22px;">
    <a href="{{ route('admin.clients.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#2563eb;color:#fff;border-radius:9px;font-size:13.5px;font-weight:600;text-decoration:none;">
        ➕ New Client
    </a>
    <a href="{{ route('admin.bookings.index') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border:1.5px solid #e2e8f0;color:#475569;background:#fff;border-radius:9px;font-size:13.5px;font-weight:600;text-decoration:none;">
        📦 All Bookings
    </a>
    <a href="{{ route('admin.rates.index') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border:1.5px solid #e2e8f0;color:#475569;background:#fff;border-radius:9px;font-size:13.5px;font-weight:600;text-decoration:none;">
        💰 Rate Cards
    </a>
    <a href="{{ route('admin.tickets.index') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border:1.5px solid #e2e8f0;background:#fff;border-radius:9px;font-size:13.5px;font-weight:600;text-decoration:none;{{ $stats['openTickets'] > 0 ? 'color:#dc2626;border-color:#fecaca;' : 'color:#475569;' }}">
        🎫 Open Tickets
        @if($stats['openTickets'] > 0)
            <span style="background:#dc2626;color:#fff;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;">{{ $stats['openTickets'] }}</span>
        @endif
    </a>
    <a href="{{ route('admin.reports.revenue') }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border:1.5px solid #e2e8f0;color:#475569;background:#fff;border-radius:9px;font-size:13.5px;font-weight:600;text-decoration:none;">
        📊 Reports
    </a>
</div>

{{-- ── Recent Bookings Table ──────────────────────────────────────────── --}}
<div class="table-card" style="margin-bottom:18px;">
    <div class="table-header">
        <span>📋 Recent Bookings</span>
        <div style="display:flex;align-items:center;gap:14px;">
            <a href="{{ route('admin.bookings.export') }}" style="font-size:12.5px;color:#64748b;text-decoration:none;">📥 Export</a>
            <a href="{{ route('admin.bookings.index') }}">View all →</a>
        </div>
    </div>

    @if($recentBookings->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">📦</div>
            <div class="empty-state-title">No bookings yet.</div>
            <p style="font-size:13px;margin-top:6px;">Bookings created by clients will appear here.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>AWB No.</th>
                    <th>Client</th>
                    <th>Destination</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $booking)
                <tr>
                    <td style="font-family:monospace;font-weight:700;">
                        <a href="{{ route('admin.bookings.show', $booking) }}" style="color:#3b82f6;text-decoration:none;">{{ $booking->awb_number }}</a>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $booking->client?->company_name ?? '—' }}</div>
                        <div style="font-size:12px;color:#64748b;">{{ $booking->client?->user?->email ?? '' }}</div>
                    </td>
                    <td>
                        {{ $booking->receiver_name }}<br>
                        <span style="font-size:12px;color:#64748b;">{{ $booking->receiver_pincode }}</span>
                    </td>
                    <td style="font-size:12.5px;">{{ str_replace('_', ' ', ucwords($booking->service_type)) }}</td>
                    <td><x-status-badge :status="$booking->booking_status" /></td>
                    <td style="font-weight:600;">₹{{ number_format($booking->total_amount, 2) }}</td>
                    <td style="color:#64748b;font-size:12.5px;">{{ $booking->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- ── Outstanding Bills + Total Revenue ─────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div style="background:#fff;border-radius:14px;border:1px solid #fecaca;padding:20px 24px;display:flex;align-items:center;gap:16px;">
        <div style="font-size:36px;">⚠️</div>
        <div>
            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Outstanding Bills</div>
            <div style="font-size:24px;font-weight:800;color:#dc2626;">₹{{ number_format($stats['outstandingBills'], 2) }}</div>
            <a href="{{ route('admin.reports.bookings', ['status' => 'booked']) }}" style="font-size:12px;color:#2563eb;text-decoration:none;margin-top:4px;display:block;">View unpaid bookings →</a>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid #bbf7d0;padding:20px 24px;display:flex;align-items:center;gap:16px;">
        <div style="font-size:36px;">💰</div>
        <div>
            <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Total Revenue (All Time)</div>
            <div style="font-size:24px;font-weight:800;color:#15803d;">₹{{ number_format($stats['totalRevenue'], 2) }}</div>
            <a href="{{ route('admin.reports.revenue') }}" style="font-size:12px;color:#2563eb;text-decoration:none;margin-top:4px;display:block;">View revenue report →</a>
        </div>
    </div>
</div>

@endsection


