@extends('client.layouts.app')
@section('page-title', 'Ticket — ' . $ticket->ticket_number)

@push('styles')
<style>
.page-wrap { max-width:820px; margin:0 auto; padding:28px 24px 60px; }
.back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; color:#64748b; text-decoration:none; margin-bottom:16px; }
.back-link:hover { color:#2563eb; }
.ticket-header { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:20px 24px; margin-bottom:16px; }
.ticket-num { font-family:monospace; font-size:20px; font-weight:800; color:#1e293b; }
.badge { display:inline-flex; padding:4px 11px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-open       { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
.badge-inprogress { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.badge-resolved   { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.badge-closed     { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
.meta-row { display:flex; gap:20px; flex-wrap:wrap; margin-top:12px; font-size:12.5px; color:#64748b; }
.meta-item strong { color:#1e293b; }
.thread-wrap { margin-bottom:20px; display:flex; flex-direction:column; gap:12px; }
.bubble-wrap { display:flex; flex-direction:column; max-width:75%; }
.bubble-wrap.client { align-self:flex-end; align-items:flex-end; }
.bubble-wrap.admin  { align-self:flex-start; align-items:flex-start; }
.bubble { padding:12px 16px; border-radius:14px; font-size:13.5px; line-height:1.6; }
.bubble.client { background:#2563eb; color:#fff; border-bottom-right-radius:4px; }
.bubble.admin  { background:#f1f5f9; color:#1e293b; border-bottom-left-radius:4px; }
.bubble-meta { font-size:11px; color:#94a3b8; margin-top:4px; }
.reply-card { background:#fff; border-radius:12px; border:1px solid #e2e8f0; padding:20px 24px; }
.reply-card h3 { font-size:14px; font-weight:700; color:#1e293b; margin-bottom:12px; }
.form-control { width:100%; padding:10px 13px; border:1.5px solid #e2e8f0; border-radius:9px; font-size:13.5px; color:#0f172a; background:#f8fafc; outline:none; font-family:inherit; }
.form-control:focus { border-color:#2563eb; background:#fff; }
.btn-send { padding:10px 24px; background:#2563eb; color:#fff; border:none; border-radius:9px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; margin-top:10px; }
.btn-send:hover { background:#1d4ed8; }
.closed-banner { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px 18px; color:#64748b; font-size:14px; margin-top:12px; text-align:center; }
</style>
@endpush

@section('content')
<div class="page-wrap">
    <a href="{{ route('client.tickets') }}" class="back-link">← Back to Tickets</a>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 18px;color:#15803d;font-size:14px;font-weight:600;margin-bottom:16px">✅ {{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="ticket-header">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <div class="ticket-num">{{ $ticket->ticket_number }}</div>
            <span class="badge badge-{{ $ticket->status }}">{{ ucfirst($ticket->status) }}</span>
        </div>
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-top:8px">{{ $ticket->subject }}</div>
        <div class="meta-row">
            @php $catLabels = ['delayed_shipment'=>'Delayed Shipment','damage'=>'Damage/Loss','wrong_delivery'=>'Wrong Delivery','invoice_issue'=>'Invoice Issue','rate_query'=>'Rate Query','other'=>'Other']; @endphp
            <span>Category: <strong>{{ $catLabels[$ticket->category] ?? $ticket->category }}</strong></span>
            @if($ticket->awb_number)<span>AWB: <strong style="font-family:monospace">{{ $ticket->awb_number }}</strong></span>@endif
            <span>Raised: <strong>{{ $ticket->created_at->format('d M Y, h:i A') }}</strong></span>
            @if($ticket->file_path)
                <a href="{{ asset('storage/' . $ticket->file_path) }}" target="_blank"
                   style="color:#2563eb;font-weight:600;text-decoration:none">📎 Download Attachment</a>
            @endif
        </div>
    </div>

    {{-- Original description --}}
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:18px 24px;margin-bottom:16px">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px">Original Query</div>
        <p style="font-size:14px;color:#374151;line-height:1.7;white-space:pre-wrap">{{ $ticket->description }}</p>
    </div>

    {{-- Thread --}}
    @if($ticket->messages->count() > 0)
    <div style="background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px 24px;margin-bottom:16px">
        <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:16px">Conversation Thread</div>
        <div class="thread-wrap">
        @foreach($ticket->messages as $msg)
            @if(!$msg->is_internal)
            <div class="bubble-wrap {{ $msg->sender_role }}">
                <div class="bubble {{ $msg->sender_role }}">{{ $msg->message }}</div>
                <div class="bubble-meta">
                    {{ $msg->sender?->name ?? ($msg->sender_role === 'admin' ? 'Support Team' : 'You') }}
                    @if($msg->sender_role === 'admin')
                        <span style="background:#e0e7ff;color:#4338ca;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:700;margin-left:4px">SUPPORT</span>
                    @endif
                    · {{ $msg->created_at->format('d M, h:i A') }}
                </div>
            </div>
            @endif
        @endforeach
        </div>
    </div>
    @endif

    {{-- Reply form --}}
    @if(in_array($ticket->status, ['open','inprogress']))
    <div class="reply-card">
        <h3>📝 Add Reply</h3>
        <form method="POST" action="{{ route('client.tickets.message', $ticket) }}">
            @csrf
            <textarea name="message" class="form-control" rows="4"
                      placeholder="Describe your query or provide additional information…" required></textarea>
            @error('message')<div style="font-size:12px;color:#ef4444;margin-top:4px">{{ $message }}</div>@enderror
            <button type="submit" class="btn-send">Send Reply →</button>
        </form>
    </div>
    @else
    <div class="closed-banner">
        🔒 This ticket is {{ $ticket->status }}. <a href="{{ route('client.tickets.create') }}" style="color:#2563eb">Open a new ticket</a> if you need further assistance.
    </div>
    @endif
</div>
@endsection
