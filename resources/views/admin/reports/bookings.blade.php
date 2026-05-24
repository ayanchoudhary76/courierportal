@extends('admin.layouts.app')
@section('page-title', 'Booking Report')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:24px 20px 60px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
        <a href="{{ route('admin.reports.index') }}" style="font-size:13px;color:#64748b;text-decoration:none">← Reports</a>
        <h1 style="font-size:22px;font-weight:800;color:#0f172a">📦 Booking Report</h1>
    </div>

    <form style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:14px 18px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px" method="GET">
        <div style="display:flex;flex-direction:column;gap:3px"><span style="font-size:11.5px;font-weight:600;color:#64748b">From</span><input type="date" name="date_from" value="{{ request('date_from', $from->format('Y-m-d')) }}" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit"></div>
        <div style="display:flex;flex-direction:column;gap:3px"><span style="font-size:11.5px;font-weight:600;color:#64748b">To</span><input type="date" name="date_to" value="{{ request('date_to', $to->format('Y-m-d')) }}" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit"></div>
        <div style="display:flex;flex-direction:column;gap:3px">
            <span style="font-size:11.5px;font-weight:600;color:#64748b">Status</span>
            <select name="status" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit">
                <option value="">All</option>
                @foreach(['booked','pickup_scheduled','picked_up','in_transit','out_for_delivery','delivered','failed','returned'] as $s)
                    <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ str_replace('_',' ',ucwords($s)) }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;flex-direction:column;gap:3px">
            <span style="font-size:11.5px;font-weight:600;color:#64748b">Client</span>
            <select name="client_id" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit">
                <option value="">All Clients</option>
                @foreach($clients as $c)
                    <option value="{{ $c->id }}" {{ request('client_id')==$c->id?'selected':'' }}>{{ $c->user?->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" style="padding:8px 18px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">🔍 Apply</button>
        <a href="{{ route('admin.reports.bookings') }}" style="padding:8px 12px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;text-decoration:none">Reset</a>
    </form>

    {{-- Status summary --}}
    @if($statusCounts->count())
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
        @php $statusColors = ['booked'=>'#b45309','pickup_scheduled'=>'#b45309','picked_up'=>'#1d4ed8','in_transit'=>'#1d4ed8','out_for_delivery'=>'#1d4ed8','delivered'=>'#15803d','failed'=>'#dc2626','returned'=>'#dc2626']; @endphp
        @foreach($statusCounts as $status => $cnt)
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:8px 14px;font-size:13px">
            <span style="font-weight:700;color:{{ $statusColors[$status]??'#475569' }}">{{ $cnt }}</span>
            <span style="color:#64748b;margin-left:4px">{{ str_replace('_',' ',ucwords($status)) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
    @if($bookings->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">No bookings found for this date range.</div>
    @else
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:13.5px">
        <thead><tr style="background:#f8fafc">
            @foreach(['AWB','Client','Route','Service','Weight','Status','Amount','Date'] as $h)
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid #e2e8f0">{{ $h }}</th>
            @endforeach
        </tr></thead>
        <tbody>
        @foreach($bookings as $b)
        <tr style="border-bottom:1px solid #f1f5f9">
            <td style="padding:10px 14px;font-family:monospace;font-weight:700;color:#2563eb"><a href="{{ route('admin.bookings.show', $b) }}" style="color:#2563eb;text-decoration:none">{{ $b->awb_number }}</a></td>
            <td style="padding:10px 14px;font-size:13px">{{ $b->client?->user?->name }}</td>
            <td style="padding:10px 14px;font-family:monospace;font-size:12px;color:#64748b">{{ $b->sender_pincode }}→{{ $b->receiver_pincode }}</td>
            <td style="padding:10px 14px;font-size:12px">{{ str_replace('_',' ',ucwords($b->service_type)) }}</td>
            <td style="padding:10px 14px;font-size:12.5px">{{ $b->weight_actual }} kg</td>
            <td style="padding:10px 14px"><span style="display:inline-flex;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:#f8fafc;color:#475569;border:1px solid #e2e8f0">{{ str_replace('_',' ',ucwords($b->booking_status)) }}</span></td>
            <td style="padding:10px 14px;font-weight:700">₹{{ number_format($b->total_amount,2) }}</td>
            <td style="padding:10px 14px;font-size:12px;color:#64748b;white-space:nowrap">{{ $b->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @if($bookings->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f1f5f9">{{ $bookings->links() }}</div>
    @endif
    @endif
    </div>
</div>
@endsection
