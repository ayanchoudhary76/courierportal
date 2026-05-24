@extends('client.layouts.app')
@section('page-title', 'My Support Tickets')

@push('styles')
<style>
.page-wrap { max-width:960px; margin:0 auto; padding:28px 24px 60px; }
.page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; }
.page-heading { font-size:22px; font-weight:800; color:#0f172a; }
.btn-new { display:inline-flex; align-items:center; gap:7px; padding:10px 20px; background:#2563eb; color:#fff; border-radius:9px; font-size:14px; font-weight:600; text-decoration:none; }
.btn-new:hover { background:#1d4ed8; }
.panel { background:#fff; border-radius:14px; border:1px solid #e2e8f0; overflow:hidden; }
table { width:100%; border-collapse:collapse; font-size:13.5px; }
thead th { background:#f8fafc; padding:11px 14px; text-align:left; font-size:11px; font-weight:700; color:#64748b; letter-spacing:0.05em; text-transform:uppercase; border-bottom:1px solid #e2e8f0; }
tbody td { padding:12px 14px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover { background:#fafcff; }
.tkt-link { font-family:monospace; font-weight:700; color:#2563eb; text-decoration:none; }
.badge { display:inline-flex; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-open       { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.badge-inprogress { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-resolved   { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-closed     { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
.empty-state { padding:60px; text-align:center; color:#94a3b8; }
.pagination-wrap { padding:14px 20px; border-top:1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="page-wrap">
    <div class="page-header">
        <div class="page-heading">🎫 My Support Tickets</div>
        <a href="{{ route('client.tickets.create') }}" class="btn-new">+ Raise New Ticket</a>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 18px;color:#15803d;font-size:14px;font-weight:600;margin-bottom:16px">✅ {{ session('success') }}</div>
    @endif

    <div class="panel">
        @if($tickets->isEmpty())
            <div class="empty-state">
                <div style="font-size:40px;margin-bottom:12px">🎫</div>
                <div style="font-size:16px;font-weight:600;color:#475569;margin-bottom:8px">No tickets yet</div>
                <p style="font-size:13.5px;margin-bottom:18px">Need help? Raise a support query and our team will respond within 24–48 hours.</p>
                <a href="{{ route('client.tickets.create') }}" style="display:inline-flex;align-items:center;gap:7px;padding:10px 22px;background:#2563eb;color:#fff;border-radius:9px;font-weight:600;text-decoration:none;font-size:14px">+ Raise Ticket</a>
            </div>
        @else
        <div style="overflow-x:auto">
        <table>
            <thead><tr>
                <th>Ticket No.</th>
                <th>Category</th>
                <th>Subject</th>
                <th>AWB</th>
                <th>Status</th>
                <th>Raised</th>
                <th>Action</th>
            </tr></thead>
            <tbody>
            @php $catLabels = ['delayed_shipment'=>'Delayed Shipment','damage'=>'Damage/Loss','wrong_delivery'=>'Wrong Delivery','invoice_issue'=>'Invoice Issue','rate_query'=>'Rate Query','other'=>'Other']; @endphp
            @foreach($tickets as $t)
            <tr>
                <td><a href="{{ route('client.tickets.show', $t) }}" class="tkt-link">{{ $t->ticket_number }}</a></td>
                <td style="font-size:12.5px">{{ $catLabels[$t->category] ?? $t->category }}</td>
                <td style="max-width:260px">
                    <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px">{{ $t->subject }}</div>
                </td>
                <td style="font-family:monospace;font-size:12.5px;color:#64748b">{{ $t->awb_number ?: '—' }}</td>
                <td><span class="badge badge-{{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                <td style="font-size:12px;color:#94a3b8;white-space:nowrap">{{ $t->created_at->format('d M Y') }}</td>
                <td><a href="{{ route('client.tickets.show', $t) }}" style="font-size:12.5px;color:#2563eb;font-weight:500;text-decoration:none">View →</a></td>
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
