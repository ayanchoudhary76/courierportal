@extends('admin.layouts.app')
@section('page-title', 'Booking Management')

@push('styles')
<style>
.filter-bar { background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:14px 18px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px; }
.fg { display:flex;flex-direction:column;gap:3px; }
.fl { font-size:11.5px;font-weight:600;color:#64748b; }
.fc { padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;color:#1e293b;background:#fff; }
.fc:focus { border-color:#2563eb; }
.fb { padding:8px 18px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;align-self:flex-end; }
.fr { padding:8px 12px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;text-decoration:none;align-self:flex-end; }
.panel { background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden; }
table { width:100%;border-collapse:collapse;font-size:13.5px; }
thead th { background:#f8fafc;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid #e2e8f0; }
tbody td { padding:11px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover { background:#fafcff; }
.awb-link { font-family:monospace;font-weight:700;color:#2563eb;text-decoration:none; }
.badge { display:inline-flex;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700; }
.badge-delivered        { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.badge-in_transit,.badge-picked_up,.badge-out_for_delivery { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
.badge-booked,.badge-pickup_scheduled { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
.badge-failed,.badge-returned { background:#fef2f2;color:#dc2626;border:1px solid #fecaca; }
.badge-default { background:#f8fafc;color:#64748b;border:1px solid #e2e8f0; }
.svc { display:inline-flex;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700; }
.svc-express_air { background:#eff6ff;color:#1d4ed8; }
.svc-priority_surface { background:#faf5ff;color:#7e22ce; }
.svc-economy_surface { background:#f0fdf4;color:#15803d; }
.pagination-wrap { padding:14px 20px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center; }
</style>
@endpush

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:24px 20px 60px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <h1 style="font-size:22px;font-weight:800;color:#0f172a">📦 Booking Management</h1>
        <span style="font-size:13px;color:#64748b">{{ $bookings->total() }} total bookings</span>
    </div>

    <form class="filter-bar" method="GET">
        <div class="fg"><span class="fl">From Date</span><input type="date" name="date_from" value="{{ request('date_from') }}" class="fc"></div>
        <div class="fg"><span class="fl">To Date</span><input type="date" name="date_to" value="{{ request('date_to') }}" class="fc"></div>
        <div class="fg">
            <span class="fl">Status</span>
            <select name="status" class="fc">
                <option value="">All Statuses</option>
                @foreach(['booked','pickup_scheduled','picked_up','in_transit','out_for_delivery','delivered','failed','returned'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ str_replace('_',' ',ucwords($s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="fg">
            <span class="fl">Service</span>
            <select name="service_type" class="fc">
                <option value="">All Services</option>
                <option value="express_air" {{ request('service_type')==='express_air'?'selected':'' }}>Express Air</option>
                <option value="priority_surface" {{ request('service_type')==='priority_surface'?'selected':'' }}>Priority Surface</option>
                <option value="economy_surface" {{ request('service_type')==='economy_surface'?'selected':'' }}>Economy Surface</option>
            </select>
        </div>
        <div class="fg"><span class="fl">Search AWB / Client</span><input type="text" name="search" value="{{ request('search') }}" class="fc" placeholder="AWB or client name" style="width:180px"></div>
        <button type="submit" class="fb">🔍 Filter</button>
        <a href="{{ route('admin.bookings.index') }}" class="fr">Reset</a>
    </form>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:12px 16px;color:#15803d;font-size:14px;font-weight:600;margin-bottom:14px">✅ {{ session('success') }}</div>
    @endif

    <div class="panel">
    @if($bookings->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">No bookings found matching your filters.</div>
    @else
    <div style="overflow-x:auto">
    <table>
        <thead><tr>
            <th>AWB No.</th><th>Client</th><th>Route</th><th>Service</th><th>Weight</th><th>Status</th><th>Amount</th><th>Date</th><th>Actions</th>
        </tr></thead>
        <tbody>
        @foreach($bookings as $b)
        @php $sc = match(true) {
            $b->booking_status==='delivered' => 'badge-delivered',
            in_array($b->booking_status,['in_transit','picked_up','out_for_delivery']) => 'badge-in_transit',
            in_array($b->booking_status,['booked','pickup_scheduled']) => 'badge-booked',
            in_array($b->booking_status,['failed','returned']) => 'badge-failed',
            default => 'badge-default',
        }; @endphp
        <tr>
            <td><a href="{{ route('admin.bookings.show', $b) }}" class="awb-link">{{ $b->awb_number }}</a></td>
            <td>
                <div style="font-weight:600;font-size:13px">{{ $b->client?->user?->name }}</div>
                <div style="font-size:11px;color:#94a3b8">{{ $b->client?->company_name }}</div>
            </td>
            <td style="font-family:monospace;font-size:12.5px;color:#64748b">{{ $b->sender_pincode }} → {{ $b->receiver_pincode }}</td>
            <td><span class="svc svc-{{ $b->service_type }}">{{ str_replace('_',' ',ucwords($b->service_type)) }}</span></td>
            <td style="font-size:12.5px">{{ $b->weight_actual }} kg</td>
            <td><span class="badge {{ $sc }}">{{ str_replace('_',' ',ucwords($b->booking_status)) }}</span></td>
            <td style="font-weight:700;font-size:13.5px">₹{{ number_format($b->total_amount,2) }}</td>
            <td style="font-size:12px;color:#64748b;white-space:nowrap">{{ $b->created_at->format('d M Y') }}</td>
            <td style="white-space:nowrap">
                <a href="{{ route('admin.bookings.show', $b) }}" style="font-size:12.5px;color:#2563eb;font-weight:500;text-decoration:none">View</a>
                &nbsp;·&nbsp;
                <a href="{{ route('admin.bookings.label', $b) }}" style="font-size:12.5px;color:#64748b;text-decoration:none">Label</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @if($bookings->hasPages())
    <div class="pagination-wrap">
        <div style="font-size:13px;color:#64748b">Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }}</div>
        {{ $bookings->links() }}
    </div>
    @endif
    @endif
    </div>
</div>
@endsection
