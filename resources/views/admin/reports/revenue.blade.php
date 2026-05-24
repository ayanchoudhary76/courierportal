@extends('admin.layouts.app')
@section('page-title', 'Revenue Report')

@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 20px 60px">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px">
        <a href="{{ route('admin.reports.index') }}" style="font-size:13px;color:#64748b;text-decoration:none">← Reports</a>
        <h1 style="font-size:22px;font-weight:800;color:#0f172a">💰 Revenue Report</h1>
    </div>

    <form style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:14px 18px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:20px" method="GET">
        <div style="display:flex;flex-direction:column;gap:3px"><span style="font-size:11.5px;font-weight:600;color:#64748b">From</span><input type="date" name="date_from" value="{{ request('date_from',$from->format('Y-m-d')) }}" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit"></div>
        <div style="display:flex;flex-direction:column;gap:3px"><span style="font-size:11.5px;font-weight:600;color:#64748b">To</span><input type="date" name="date_to" value="{{ request('date_to',$to->format('Y-m-d')) }}" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit"></div>
        <button type="submit" style="padding:8px 18px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">Apply</button>
        <a href="{{ route('admin.reports.revenue') }}" style="padding:8px 12px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;text-decoration:none">Reset</a>
    </form>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        {{-- Daily table --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
            <div style="padding:14px 18px;font-size:13px;font-weight:700;color:#1e293b;border-bottom:1px solid #f1f5f9">📅 Daily Revenue</div>
            @if($daily->isEmpty())
                <div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px">No data for this period.</div>
            @else
            <table style="width:100%;border-collapse:collapse;font-size:13.5px">
                <thead><tr style="background:#f8fafc">
                    <th style="padding:9px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Date</th>
                    <th style="padding:9px 14px;text-align:right;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Bookings</th>
                    <th style="padding:9px 14px;text-align:right;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Revenue</th>
                </tr></thead>
                <tbody>
                @php $totalRev = 0; $totalBk = 0; @endphp
                @foreach($daily as $d)
                @php $totalRev += $d->revenue; $totalBk += $d->count; @endphp
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:9px 14px;font-weight:600;color:#1e293b">{{ \Carbon\Carbon::parse($d->date)->format('d M Y') }}</td>
                    <td style="padding:9px 14px;text-align:right;color:#64748b">{{ $d->count }}</td>
                    <td style="padding:9px 14px;text-align:right;font-weight:700;color:#15803d">₹{{ number_format($d->revenue,2) }}</td>
                </tr>
                @endforeach
                <tr style="background:#f8fafc">
                    <td style="padding:9px 14px;font-weight:800;color:#0f172a">Total</td>
                    <td style="padding:9px 14px;text-align:right;font-weight:800">{{ $totalBk }}</td>
                    <td style="padding:9px 14px;text-align:right;font-weight:800;color:#1e40af">₹{{ number_format($totalRev,2) }}</td>
                </tr>
                </tbody>
            </table>
            @endif
        </div>

        {{-- Top clients --}}
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden">
            <div style="padding:14px 18px;font-size:13px;font-weight:700;color:#1e293b;border-bottom:1px solid #f1f5f9">🏆 Top 10 Clients by Revenue</div>
            @if($topClients->isEmpty())
                <div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px">No data yet.</div>
            @else
            <table style="width:100%;border-collapse:collapse;font-size:13.5px">
                <thead><tr style="background:#f8fafc">
                    <th style="padding:9px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">#</th>
                    <th style="padding:9px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Client</th>
                    <th style="padding:9px 14px;text-align:right;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Bookings</th>
                    <th style="padding:9px 14px;text-align:right;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;border-bottom:1px solid #e2e8f0">Revenue</th>
                </tr></thead>
                <tbody>
                @foreach($topClients as $i => $c)
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:9px 14px;color:#94a3b8;font-weight:700">{{ $i+1 }}</td>
                    <td style="padding:9px 14px">
                        <div style="font-weight:600;font-size:13px">{{ $c->user?->name }}</div>
                        <div style="font-size:11px;color:#94a3b8">{{ $c->company_name }}</div>
                    </td>
                    <td style="padding:9px 14px;text-align:right;color:#64748b">{{ $c->bookings_count }}</td>
                    <td style="padding:9px 14px;text-align:right;font-weight:700;color:#15803d">₹{{ number_format($c->bookings_sum_total_amount ?? 0,2) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection
