@extends('admin.layouts.app')
@section('page-title', 'Ticket — ' . $ticket->ticket_number)

@push('styles')
<style>
.two-col { display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start; }
@media(max-width:900px) { .two-col { grid-template-columns:1fr; } }
.card { background:#fff;border-radius:12px;border:1px solid #e2e8f0;padding:20px;margin-bottom:14px; }
.card-title { font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #f1f5f9; }
.badge { display:inline-flex;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700; }
.badge-open       { background:#fef2f2;color:#dc2626;border:1px solid #fecaca; }
.badge-inprogress { background:#fffbeb;color:#b45309;border:1px solid #fde68a; }
.badge-resolved   { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.badge-closed     { background:#f8fafc;color:#64748b;border:1px solid #e2e8f0; }
.badge-admin { background:#e0e7ff;color:#4338ca;font-size:10px;padding:1px 7px;border-radius:10px;font-weight:700;margin-left:4px; }
.badge-client { background:#f0fdf4;color:#15803d;font-size:10px;padding:1px 7px;border-radius:10px;font-weight:700;margin-left:4px; }
/* Thread */
.thread { display:flex;flex-direction:column;gap:12px;margin-bottom:18px; }
.bw { display:flex;flex-direction:column;max-width:78%; }
.bw.client { align-self:flex-end;align-items:flex-end; }
.bw.admin  { align-self:flex-start;align-items:flex-start; }
.bubble { padding:11px 15px;border-radius:12px;font-size:13.5px;line-height:1.6; }
.bubble.client  { background:#2563eb;color:#fff;border-bottom-right-radius:3px; }
.bubble.admin   { background:#f1f5f9;color:#1e293b;border-bottom-left-radius:3px; }
.bubble.internal{ background:#fef9c3;color:#854d0e;border:1px solid #fde047;border-bottom-left-radius:3px; }
.bm { font-size:11px;color:#94a3b8;margin-top:4px; }
/* Form */
.fc { width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13.5px;color:#1e293b;outline:none;font-family:inherit;background:#f8fafc; }
.fc:focus { border-color:#2563eb; }
.fb { padding:9px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:13.5px;font-weight:600;cursor:pointer;font-family:inherit; }
.fb:hover { background:#1d4ed8; }
.fb-sm { padding:7px 14px;font-size:12.5px; }
.info-row { margin-bottom:10px; }
.info-label { font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:2px; }
.info-val { font-size:13.5px;font-weight:600;color:#1e293b; }
</style>
@endpush

@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 20px 60px">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px">
    <div>
        <a href="{{ route('admin.tickets.index') }}" style="font-size:13px;color:#64748b;text-decoration:none">← All Tickets</a>
        <div style="font-size:22px;font-weight:800;color:#0f172a;margin-top:4px;font-family:monospace">{{ $ticket->ticket_number }}</div>
    </div>
    <span class="badge badge-{{ $ticket->status }}" style="font-size:13px;padding:6px 16px">{{ ucfirst($ticket->status) }}</span>
</div>

@if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:12px 16px;color:#15803d;font-size:14px;font-weight:600;margin-bottom:14px">✅ {{ session('success') }}</div>
@endif

<div class="two-col">
    {{-- LEFT: Thread + Reply --}}
    <div>
        {{-- Subject + Description --}}
        <div class="card">
            <div style="font-size:16px;font-weight:800;color:#1e293b;margin-bottom:8px">{{ $ticket->subject }}</div>
            <p style="font-size:14px;color:#374151;line-height:1.7;white-space:pre-wrap">{{ $ticket->description }}</p>
            @if($ticket->file_path)
                <a href="{{ asset('storage/'.$ticket->file_path) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;font-size:13px;color:#2563eb;font-weight:500;text-decoration:none">📎 View Attachment</a>
            @endif
        </div>

        {{-- Thread --}}
        @if($ticket->messages->count() > 0)
        <div class="card">
            <div class="card-title">Conversation Thread</div>
            <div class="thread">
            @foreach($ticket->messages as $msg)
            <div class="bw {{ $msg->sender_role }}">
                <div class="bubble {{ $msg->is_internal ? 'internal' : $msg->sender_role }}">
                    @if($msg->is_internal)<span style="font-size:10px;font-weight:700;opacity:0.7">🔒 INTERNAL NOTE — </span>@endif
                    {{ $msg->message }}
                </div>
                <div class="bm">
                    {{ $msg->sender?->name ?? ($msg->sender_role === 'admin' ? 'Support' : 'Client') }}
                    <span class="badge-{{ $msg->sender_role }}">{{ strtoupper($msg->sender_role) }}</span>
                    · {{ $msg->created_at->format('d M, h:i A') }}
                </div>
            </div>
            @endforeach
            </div>
        </div>
        @endif

        {{-- Reply form --}}
        <div class="card">
            <div class="card-title">Add Reply</div>
            <form method="POST" action="{{ route('admin.tickets.message', $ticket) }}" x-data="{ internal: false }">
                @csrf
                <textarea name="message" class="fc" rows="4" placeholder="Type your reply…" required style="margin-bottom:10px"></textarea>
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
                    <label style="display:flex;align-items:center;gap:7px;font-size:13px;color:#475569;cursor:pointer">
                        <input type="checkbox" name="internal_note" x-model="internal" style="accent-color:#f59e0b">
                        <span :style="internal ? 'color:#b45309;font-weight:600' : ''">🔒 Internal Note (only visible to admins)</span>
                    </label>
                    <button type="submit" class="fb">Send Reply →</button>
                </div>
            </form>
        </div>
    </div>

    {{-- RIGHT: Info + Actions --}}
    <div>
        {{-- Ticket Info --}}
        <div class="card">
            <div class="card-title">Ticket Information</div>
            <div class="info-row"><div class="info-label">Client</div><div class="info-val">{{ $ticket->client?->user?->name }}</div></div>
            <div class="info-row"><div class="info-label">Company</div><div class="info-val">{{ $ticket->client?->company_name }}</div></div>
            @if($ticket->awb_number)
            <div class="info-row"><div class="info-label">AWB</div>
                <div class="info-val">
                    <a href="{{ route('admin.bookings.show', Booking::where('awb_number',$ticket->awb_number)->first() ?? 0) }}"
                       style="color:#2563eb;text-decoration:none;font-family:monospace">{{ $ticket->awb_number }}</a>
                </div>
            </div>
            @endif
            @php $catLabels = ['delayed_shipment'=>'Delayed Shipment','damage'=>'Damage/Loss','wrong_delivery'=>'Wrong Delivery','invoice_issue'=>'Invoice Issue','rate_query'=>'Rate Query','other'=>'Other']; @endphp
            <div class="info-row"><div class="info-label">Category</div><div class="info-val">{{ $catLabels[$ticket->category] ?? $ticket->category }}</div></div>
            <div class="info-row"><div class="info-label">Created</div><div class="info-val" style="font-size:12.5px">{{ $ticket->created_at->format('d M Y, h:i A') }}</div></div>
            <div class="info-row"><div class="info-label">Last Updated</div><div class="info-val" style="font-size:12.5px">{{ $ticket->updated_at->diffForHumans() }}</div></div>
            <div class="info-row"><div class="info-label">Assigned To</div><div class="info-val">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</div></div>
        </div>

        {{-- Update Status --}}
        <div class="card">
            <div class="card-title">Update Status</div>
            <div style="display:flex;gap:8px">
                <select id="status-select" class="fc">
                    @foreach(['open','inprogress','resolved','closed'] as $s)
                        <option value="{{ $s }}" {{ $ticket->status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button class="fb fb-sm" onclick="updateStatus()">Update</button>
            </div>
        </div>

        {{-- Assign --}}
        <div class="card">
            <div class="card-title">Assign to Admin</div>
            <div style="display:flex;gap:8px">
                <select id="assign-select" class="fc">
                    <option value="">— Unassigned —</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ $ticket->assigned_to===$admin->id?'selected':'' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
                <button class="fb fb-sm" onclick="assignAdmin()">Assign</button>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="card">
            <div class="card-title">Quick Links</div>
            <a href="{{ route('admin.clients.show', $ticket->client_id) }}" style="display:block;font-size:13px;color:#2563eb;text-decoration:none;margin-bottom:8px">👤 View Client Profile</a>
            @if($ticket->awb_number)
                @php $bk = \App\Models\Booking::where('awb_number',$ticket->awb_number)->first(); @endphp
                @if($bk)
                <a href="{{ route('admin.bookings.show', $bk) }}" style="display:block;font-size:13px;color:#2563eb;text-decoration:none">📦 View Booking</a>
                @endif
            @endif
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name=csrf-token]').content;
function updateStatus() {
    const s = document.getElementById('status-select').value;
    fetch('{{ route('admin.tickets.status', $ticket) }}', {
        method: 'PUT',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ status: s })
    }).then(r => r.json()).then(d => { if(d.success) location.reload(); });
}
function assignAdmin() {
    const id = document.getElementById('assign-select').value;
    fetch('{{ route('admin.tickets.assign', $ticket) }}', {
        method: 'PUT',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ admin_id: id })
    }).then(r => r.json()).then(d => { if(d.success) location.reload(); });
}
</script>
@endpush
@endsection
