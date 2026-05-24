@extends('admin.layouts.app')

@section('page-title')
    📦 Bookings — {{ $client->company_name }}
@endsection

@push('styles')
<style>
    .page-actions { display:flex; gap:10px; justify-content:flex-end; margin-bottom:20px; }
    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px; font-size:13.5px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:opacity 0.15s; }
    .btn-outline { background:#fff; color:#475569; border:1.5px solid #e2e8f0; }
    .btn-outline:hover { background:#f8fafc; }
    .table-card { background:#fff; border-radius:14px; border:1px solid #e2e8f0; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
    .table-meta { padding:14px 20px; border-bottom:1px solid #f1f5f9; font-size:13px; color:#64748b; }
    table { width:100%; border-collapse:collapse; }
    thead th { background:#f8fafc; padding:11px 16px; text-align:left; font-size:11.5px; font-weight:700; color:#64748b; letter-spacing:0.05em; text-transform:uppercase; border-bottom:1px solid #e2e8f0; white-space:nowrap; }
    tbody td { padding:12px 16px; font-size:13.5px; color:#1e293b; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafcff; }
    .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .badge-green  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .badge-red    { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .badge-yellow { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
    .badge-gray   { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
    .empty-msg { padding:50px; text-align:center; color:#94a3b8; font-size:13.5px; }
    .pagination-wrap { padding:16px 20px; border-top:1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="page-actions">
    <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-outline">← Back to Client</a>
</div>

<div class="table-card">
    <div class="table-meta">
        <strong>{{ $bookings->total() }}</strong> total bookings for <strong>{{ $client->company_name }}</strong>
    </div>

    @if($bookings->isEmpty())
        <div class="empty-msg">📦 No bookings found for this client.</div>
    @else
    <table>
        <thead><tr>
            <th>AWB No.</th>
            <th>Receiver</th>
            <th>Destination</th>
            <th>Service</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Amount</th>
            <th>Date</th>
        </tr></thead>
        <tbody>
        @foreach($bookings as $booking)
        <tr>
            <td style="font-family:monospace;font-weight:700;color:#3b82f6">{{ $booking->awb_number }}</td>
            <td>{{ $booking->receiver_name }}</td>
            <td>{{ $booking->receiver_pincode }}</td>
            <td><span class="badge badge-gray">{{ str_replace('_',' ',ucwords($booking->service_type)) }}</span></td>
            <td>
                @php $sc = match($booking->booking_status) {
                    'delivered' => 'badge-green',
                    'in_transit','picked_up' => 'badge-blue',
                    'failed','returned' => 'badge-red',
                    default => 'badge-yellow',
                }; @endphp
                <span class="badge {{ $sc }}">{{ str_replace('_',' ',ucwords($booking->booking_status)) }}</span>
            </td>
            <td>
                @php $pc = match($booking->payment_status) {
                    'paid'    => 'badge-green',
                    'pending' => 'badge-yellow',
                    'partial' => 'badge-blue',
                    default   => 'badge-gray',
                }; @endphp
                <span class="badge {{ $pc }}">{{ ucfirst($booking->payment_status) }}</span>
            </td>
            <td style="font-weight:600">₹{{ number_format($booking->total_amount,2) }}</td>
            <td style="color:#64748b;font-size:12.5px">{{ $booking->created_at->format('d M Y') }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @if($bookings->hasPages())
    <div class="pagination-wrap">{{ $bookings->links() }}</div>
    @endif
    @endif
</div>
@endsection
