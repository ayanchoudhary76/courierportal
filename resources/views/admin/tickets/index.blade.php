@extends('admin.layouts.app')
@section('page-title', 'Support Tickets')

@push('styles')
<style>
.filter-bar { background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:14px 20px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:16px; }
.fc { padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;outline:none;font-family:inherit;color:#1e293b; }
.fc:focus { border-color:#2563eb; }
.fb { padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer; }
.fr { padding:8px 12px;background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;text-decoration:none; }
.tab-bar { display:flex;gap:4px;margin-bottom:14px;flex-wrap:wrap; }
.tab-btn { padding:7px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:1.5px solid #e2e8f0;background:#fff;color:#64748b;text-decoration:none; }
.tab-btn.active { background:#2563eb;color:#fff;border-color:#2563eb; }
.tab-count { display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;font-size:10px;font-weight:700;margin-left:4px; }
.count-open { background:#fecaca;color:#dc2626; }
.count-inprogress { background:#fde68a;color:#b45309; }
.count-resolved { background:#bbf7d0;color:#15803d; }
.count-closed { background:#e2e8f0;color:#64748b; }
.panel { background:#fff;border-radius:12px;border:1px solid #e2e8f0;overflow:hidden; }
table { width:100%;border-collapse:collapse;font-size:13.5px; }
thead th { background:#f8fafc;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;border-bottom:1px solid #e2e8f0; }
tbody td { padding:11px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover { background:#fafcff; }
tbody tr.sla-alert { background:#fff5f5; }
.badge { display:inline-flex;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700; }
.badge-open       { background:#fef2f2;color:#dc2626;border:1px solid #fecaca; }
.badge-inprogress { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
.badge-resolved   { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.badge-closed     { background:#f8fafc;color:#64748b;border:1px solid #e2e8f0; }
.tkt-link { font-family:monospace;font-weight:700;color:#2563eb;text-decoration:none; }
.pagination-wrap { padding:14px 20px;border-top:1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
        <h1 style="font-size:22px;font-weight:800;color:#0f172a">🎫 Support Tickets</h1>
        <div style="font-size:13px;color:#64748b">{{ $tickets->total() }} total tickets</div>
    </div>

    {{-- Status tabs --}}
    <div class="tab-bar">
        @php $cur = request('status'); @endphp
        <a href="{{ request()->url() }}" class="tab-btn {{ !$cur ? 'active':'' }}">All <span class="tab-count" style="background:#e2e8f0;color:#475569">{{ $statusCounts->sum() }}</span></a>
        <a href="{{ request()->fullUrlWithQuery(['status'=>'open']) }}" class="tab-btn {{ $cur==='open'?'active':'' }}">Open <span class="tab-count count-open">{{ $statusCounts['open']??0 }}</span></a>
        <a href="{{ request()->fullUrlWithQuery(['status'=>'inprogress']) }}" class="tab-btn {{ $cur==='inprogress'?'active':'' }}">In Progress <span class="tab-count count-inprogress">{{ $statusCounts['inprogress']??0 }}</span></a>
        <a href="{{ request()->fullUrlWithQuery(['status'=>'resolved']) }}" class="tab-btn {{ $cur==='resolved'?'active':'' }}">Resolved <span class="tab-count count-resolved">{{ $statusCounts['resolved']??0 }}</span></a>
        <a href="{{ request()->fullUrlWithQuery(['status'=>'closed']) }}" class="tab-btn {{ $cur==='closed'?'active':'' }}">Closed <span class="tab-count count-closed">{{ $statusCounts['closed']??0 }}</span></a>
    </div>

    {{-- Filter --}}
    <form class="filter-bar" method="GET">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <select name="category" class="fc">
            <option value="">All Categories</option>
            @foreach(['delayed_shipment'=>'Delayed Shipment','damage'=>'Damage/Loss','wrong_delivery'=>'Wrong Delivery','invoice_issue'=>'Invoice Issue','rate_query'=>'Rate Query','other'=>'Other'] as $v=>$l)
                <option value="{{ $v }}" {{ request('category')===$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <button type="submit" class="fb">🔍 Filter</button>
        <a href="{{ route('admin.tickets.index') }}" class="fr">Reset</a>
    </form>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:12px 16px;color:#15803d;font-size:14px;font-weight:600;margin-bottom:14px">✅ {{ session('success') }}</div>
    @endif

    <div class="panel">
    @if($tickets->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">No tickets found.</div>
    @else
    <div style="overflow-x:auto">
    <table>
        <thead><tr>
            <th>Ticket No.</th><th>Client</th><th>Category</th><th>Subject</th><th>Status</th><th>Age</th><th>Assigned</th><th>Action</th>
        </tr></thead>
        <tbody>
        @php $catLabels = ['delayed_shipment'=>'Delayed Shipment','damage'=>'Damage/Loss','wrong_delivery'=>'Wrong Delivery','invoice_issue'=>'Invoice Issue','rate_query'=>'Rate Query','other'=>'Other']; @endphp
        @foreach($tickets as $t)
        @php $isSlaAlert = $t->status === 'open' && $t->created_at->lt(now()->subHours(48)); @endphp
        <tr class="{{ $isSlaAlert ? 'sla-alert' : '' }}">
            <td><a href="{{ route('admin.tickets.show', $t) }}" class="tkt-link">{{ $t->ticket_number }}</a>{{ $isSlaAlert ? ' ⚠️' : '' }}</td>
            <td>
                <div style="font-weight:600">{{ $t->client?->user?->name }}</div>
                <div style="font-size:11.5px;color:#94a3b8">{{ $t->client?->company_name }}</div>
            </td>
            <td style="font-size:12.5px">{{ $catLabels[$t->category] ?? $t->category }}</td>
            <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:600">{{ $t->subject }}</td>
            <td><span class="badge badge-{{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
            <td style="font-size:12px;color:#64748b;white-space:nowrap">{{ $t->created_at->diffForHumans() }}</td>
            <td style="font-size:12.5px;color:#64748b">{{ $t->assignedTo?->name ?? '—' }}</td>
            <td><a href="{{ route('admin.tickets.show', $t) }}" style="font-size:12.5px;color:#2563eb;font-weight:500;text-decoration:none">View →</a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    @if($tickets->hasPages())
        <div class="pagination-wrap">{{ $tickets->links() }}</div>
    @endif
    @endif
    </div>
</div>
@endsection
