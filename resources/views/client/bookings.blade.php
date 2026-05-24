@extends('client.layouts.app')
@section('page-title', 'My Bookings')

@push('styles')
<style>
.page-wrap { max-width:1100px; margin:0 auto; padding:28px 24px 60px; }
.page-heading { font-size:22px; font-weight:800; color:#0f172a; margin-bottom:18px; }
.filter-bar { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:16px 20px; display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; margin-bottom:18px; }
.filter-group { display:flex; flex-direction:column; gap:4px; }
.filter-label { font-size:11.5px; font-weight:600; color:#64748b; }
.filter-control { padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; font-family:inherit; color:#1e293b; }
.filter-control:focus { border-color:#2563eb; }
.filter-btn { padding:8px 18px; background:#2563eb; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; align-self:flex-end; }
.filter-btn:hover { background:#1d4ed8; }
.filter-reset { padding:8px 14px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; align-self:flex-end; text-decoration:none; }

.panel { background:#fff; border-radius:14px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
table { width:100%; border-collapse:collapse; font-size:13.5px; }
thead th { background:#f8fafc; padding:11px 14px; text-align:left; font-size:11px; font-weight:700; color:#64748b; letter-spacing:0.05em; text-transform:uppercase; border-bottom:1px solid #e2e8f0; }
tbody td { padding:12px 14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover { background:#fafcff; }
.awb-link { font-family:monospace; font-weight:700; color:#2563eb; text-decoration:none; }
.awb-link:hover { text-decoration:underline; }
.badge { display:inline-flex; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-delivered  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-in_transit,.badge-picked_up,.badge-out_for_delivery { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
.badge-booked,.badge-pickup_scheduled { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-failed,.badge-returned { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.badge-default { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
.action-link { font-size:12px; color:#2563eb; text-decoration:none; font-weight:500; white-space:nowrap; }
.action-link:hover { text-decoration:underline; }
.empty-state { padding:60px; text-align:center; color:#94a3b8; }
.empty-icon { font-size:40px; margin-bottom:12px; }
.pagination-wrap { padding:16px 20px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; }
</style>
@endpush

@section('content')
<div class="page-wrap">
    <div class="page-heading">📋 My Bookings</div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('client.bookings') }}">
        <div class="filter-bar">
            <div class="filter-group">
                <span class="filter-label">From Date</span>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-control">
            </div>
            <div class="filter-group">
                <span class="filter-label">To Date</span>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-control">
            </div>
            <div class="filter-group">
                <span class="filter-label">Status</span>
                <select name="status" class="filter-control">
                    <option value="">All Statuses</option>
                    @foreach(['booked','pickup_scheduled','picked_up','in_transit','out_for_delivery','delivered','failed','returned'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ str_replace('_',' ',ucwords($s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Search AWB / Receiver</span>
                <input type="text" name="search" value="{{ request('search') }}" class="filter-control" placeholder="AWB or receiver name" style="width:200px">
            </div>
            <button type="submit" class="filter-btn">🔍 Filter</button>
            <a href="{{ route('client.bookings') }}" class="filter-reset">✕ Reset</a>
        </div>
    </form>

    <div class="panel">
        @if($bookings->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <div style="font-size:16px;font-weight:600;color:#475569;margin-bottom:8px">No bookings found</div>
                <p style="font-size:13.5px;margin-bottom:18px">
                    @if(request()->hasAny(['status','from_date','to_date','search']))
                        No bookings match your filters. <a href="{{ route('client.bookings') }}" style="color:#2563eb">Clear filters</a>
                    @else
                        You haven't made any bookings yet.
                    @endif
                </p>
                <a href="{{ route('client.book') }}" style="display:inline-flex;align-items:center;gap:7px;padding:10px 22px;background:#2563eb;color:#fff;border-radius:9px;font-weight:600;text-decoration:none;font-size:14px">📦 Book Now</a>
            </div>
        @else
        <div style="overflow-x:auto">
        <table>
            <thead><tr>
                <th>AWB No.</th>
                <th>Date</th>
                <th>Receiver</th>
                <th>Destination</th>
                <th>Service</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr></thead>
            <tbody>
            @foreach($bookings as $b)
            <tr>
                <td><a href="{{ route('client.bookings.show', $b->awb_number) }}" class="awb-link">{{ $b->awb_number }}</a></td>
                <td style="color:#64748b;font-size:12.5px;white-space:nowrap">{{ $b->created_at->format('d M Y') }}</td>
                <td>
                    <div style="font-weight:600">{{ $b->receiver_name }}</div>
                    <div style="font-size:11.5px;color:#94a3b8">{{ $b->receiver_phone }}</div>
                </td>
                <td style="font-family:monospace;color:#64748b">{{ $b->receiver_pincode }}</td>
                <td style="font-size:12px;white-space:nowrap">{{ str_replace('_',' ', ucwords($b->service_type)) }}</td>
                <td>
                    @php $sc = match(true) {
                        $b->booking_status === 'delivered'       => 'badge-delivered',
                        in_array($b->booking_status,['in_transit','picked_up','out_for_delivery']) => 'badge-in_transit',
                        in_array($b->booking_status,['booked','pickup_scheduled']) => 'badge-booked',
                        in_array($b->booking_status,['failed','returned']) => 'badge-failed',
                        default => 'badge-default',
                    }; @endphp
                    <span class="badge {{ $sc }}">{{ str_replace('_',' ', ucwords($b->booking_status)) }}</span>
                </td>
                <td style="font-weight:700;white-space:nowrap">₹{{ number_format($b->total_amount,2) }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ route('tracking.public', $b->awb_number) }}" class="action-link">📡 Track</a>
                    &nbsp;·&nbsp;
                    <a href="{{ route('client.bookings.label', $b->awb_number) }}" class="action-link">📥 Label</a>
                    &nbsp;·&nbsp;
                    <a href="{{ route('client.bookings.show', $b->awb_number) }}" class="action-link">👁 View</a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @if($bookings->hasPages())
        <div class="pagination-wrap">
            <div style="font-size:13px;color:#64748b">
                Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings
            </div>
            {{ $bookings->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
