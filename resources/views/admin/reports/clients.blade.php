@extends('admin.layouts.app')
@section('page-title', 'Client Activity Report')

@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 20px 60px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
        <a href="{{ route('admin.reports.index') }}" style="font-size:13px;color:#64748b;text-decoration:none">← Reports</a>
        <h1 style="font-size:22px;font-weight:800;color:#0f172a">👥 Client Activity Report</h1>
    </div>

    <form style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:14px 18px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or company" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;width:240px">
        <button type="submit" style="padding:8px 18px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">🔍 Search</button>
        <a href="{{ route('admin.reports.clients') }}" style="padding:8px 12px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;text-decoration:none">Reset</a>
    </form>

    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
    @if($clients->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">No clients found.</div>
    @else
    <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse;font-size:13.5px">
        <thead><tr style="background:#f8fafc">
            @foreach(['Client','Company','City','Account','Bookings','Total Revenue','Last Booking','Action'] as $h)
            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;border-bottom:1px solid #e2e8f0">{{ $h }}</th>
            @endforeach
        </tr></thead>
        <tbody>
        @foreach($clients as $c)
        <tr style="border-bottom:1px solid #f1f5f9">
            <td style="padding:10px 14px">
                <div style="font-weight:700;color:#1e293b;font-size:13px">{{ $c->user?->name }}</div>
                <div style="font-size:11.5px;color:#94a3b8">{{ $c->user?->email }}</div>
            </td>
            <td style="padding:10px 14px;font-size:13px;color:#475569">{{ $c->company_name ?: '—' }}</td>
            <td style="padding:10px 14px;font-size:13px;color:#475569">{{ $c->city }}</td>
            <td style="padding:10px 14px">
                <span style="display:inline-flex;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $c->account_type==='credit'?'#eff6ff':'#f8fafc' }};color:{{ $c->account_type==='credit'?'#1d4ed8':'#64748b' }};border:1px solid {{ $c->account_type==='credit'?'#bfdbfe':'#e2e8f0' }}">{{ ucfirst($c->account_type) }}</span>
            </td>
            <td style="padding:10px 14px;font-weight:700;font-size:14px;color:#1e293b">{{ $c->bookings_count }}</td>
            <td style="padding:10px 14px;font-weight:700;color:#15803d">₹{{ number_format($c->bookings_sum_total_amount ?? 0,2) }}</td>
            <td style="padding:10px 14px;font-size:12px;color:#64748b">
                @php $lb = $c->bookings()->latest()->first(); @endphp
                {{ $lb ? $lb->created_at->format('d M Y') : '—' }}
            </td>
            <td style="padding:10px 14px">
                <a href="{{ route('admin.clients.show', $c) }}" style="font-size:12.5px;color:#2563eb;font-weight:500;text-decoration:none">View →</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @if($clients->hasPages())
        <div style="padding:14px 20px;border-top:1px solid #f1f5f9">{{ $clients->links() }}</div>
    @endif
    @endif
    </div>
</div>
@endsection
