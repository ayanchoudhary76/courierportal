@extends('admin.layouts.app')
@section('page-title', 'Reports')

@section('content')
<div style="max-width:1000px;margin:0 auto;padding:24px 20px 60px">
    <h1 style="font-size:22px;font-weight:800;color:#0f172a;margin-bottom:22px">📊 Reports</h1>

    {{-- Summary cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px">
        @foreach([
            ['label'=>'Bookings This Month','value'=>$total_bookings_this_month,'icon'=>'📦','color'=>'#2563eb'],
            ['label'=>'Revenue This Month','value'=>'₹'.number_format($total_revenue_this_month,0),'icon'=>'💰','color'=>'#15803d'],
            ['label'=>'Active Clients','value'=>$total_clients,'icon'=>'👥','color'=>'#7e22ce'],
            ['label'=>'Open Tickets','value'=>$active_tickets,'icon'=>'🎫','color'=>'#b45309'],
        ] as $card)
        <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px">
            <div style="font-size:28px;margin-bottom:8px">{{ $card['icon'] }}</div>
            <div style="font-size:22px;font-weight:800;color:{{ $card['color'] }}">{{ $card['value'] }}</div>
            <div style="font-size:12px;color:#64748b;font-weight:600;margin-top:4px">{{ $card['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Report links --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
        @foreach([
            ['route'=>'admin.reports.bookings','title'=>'📦 Booking Report','desc'=>'Filter and analyse bookings by date, status, and service type.'],
            ['route'=>'admin.reports.revenue', 'title'=>'💰 Revenue Report','desc'=>'Daily revenue breakdown and top client analysis.'],
            ['route'=>'admin.reports.clients', 'title'=>'👥 Client Activity','desc'=>'Client-wise booking counts and total revenue earned.'],
        ] as $link)
        <a href="{{ route($link['route']) }}" style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px;text-decoration:none;display:block;transition:box-shadow 0.15s">
            <div style="font-size:17px;font-weight:800;color:#1e293b;margin-bottom:6px">{{ $link['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.5">{{ $link['desc'] }}</div>
            <div style="font-size:13px;color:#2563eb;font-weight:600;margin-top:12px">View Report →</div>
        </a>
        @endforeach
    </div>
</div>
@endsection
