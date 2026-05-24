@extends('admin.layouts.app')

@section('page-title')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6">
        <path d="M9 7H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3"/>
        <rect x="9" y="3" width="6" height="6" rx="1"/>
        <path d="M9 12h6M9 16h4"/>
    </svg>
    Rate Cards
@endsection

@push('styles')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; flex-wrap:wrap; gap:12px; }
    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; border-radius:9px; font-size:13.5px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:opacity 0.15s,transform 0.1s; }
    .btn:active { transform:scale(0.98); }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { opacity:0.88; }
    .btn-secondary { background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; }
    .btn-secondary:hover { background:#e2e8f0; }
    .btn-danger { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .btn-danger:hover { background:#fee2e2; }
    .btn-sm { padding:6px 13px; font-size:12px; border-radius:7px; }
    .btn-warning { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
    .btn-warning:hover { background:#fef3c7; }

    .cards-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
    @media(max-width:1100px) { .cards-grid { grid-template-columns:repeat(2,1fr); } }
    @media(max-width:700px)  { .cards-grid { grid-template-columns:1fr; } }

    .rate-card {
        background:#fff; border-radius:16px; border:1px solid #e2e8f0;
        box-shadow:0 1px 4px rgba(0,0,0,0.05);
        transition:box-shadow 0.2s, transform 0.15s;
        overflow:hidden; display:flex; flex-direction:column;
    }
    .rate-card:hover { box-shadow:0 8px 24px rgba(0,0,0,0.09); transform:translateY(-2px); }

    .rc-header {
        padding:20px 20px 16px;
        border-bottom:1px solid #f1f5f9;
        display:flex; align-items:flex-start; justify-content:space-between; gap:10px;
    }
    .rc-name { font-size:15px; font-weight:700; color:#0f172a; margin-bottom:5px; }
    .rc-desc { font-size:12.5px; color:#64748b; line-height:1.4; }
    .badge-default {
        background:linear-gradient(135deg,#fbbf24,#f59e0b);
        color:#fff; font-size:10px; font-weight:800;
        padding:3px 9px; border-radius:20px;
        letter-spacing:0.06em; text-transform:uppercase;
        white-space:nowrap; flex-shrink:0;
        box-shadow:0 2px 6px rgba(245,158,11,0.35);
    }

    .rc-stats { padding:14px 20px; display:flex; gap:20px; border-bottom:1px solid #f1f5f9; }
    .rc-stat { }
    .rc-stat-val { font-size:18px; font-weight:800; color:#1e293b; }
    .rc-stat-lbl { font-size:11px; color:#94a3b8; font-weight:500; margin-top:2px; }

    .rc-meta { padding:10px 20px; font-size:12px; color:#94a3b8; border-bottom:1px solid #f1f5f9; }

    .rc-actions { padding:14px 20px; display:flex; gap:8px; flex-wrap:wrap; margin-top:auto; }

    .empty-state { padding:80px 20px; text-align:center; }
    .empty-state-icon { font-size:48px; margin-bottom:14px; }
    .empty-state-title { font-size:17px; font-weight:700; color:#1e293b; margin-bottom:8px; }
    .empty-state-sub { font-size:13.5px; color:#94a3b8; margin-bottom:20px; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div style="font-size:13.5px;color:#64748b">
        <strong>{{ $rateCards->count() }}</strong> rate card{{ $rateCards->count() !== 1 ? 's' : '' }} configured
    </div>
    <a href="{{ route('admin.rates.create') }}" class="btn btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Create New Rate Card
    </a>
</div>

@if($rateCards->isEmpty())
    <div class="empty-state">
        <div class="empty-state-icon">📋</div>
        <div class="empty-state-title">No rate cards yet</div>
        <p class="empty-state-sub">Create your first rate card to start pricing domestic and international shipments.</p>
        <a href="{{ route('admin.rates.create') }}" class="btn btn-primary">+ Create Rate Card</a>
    </div>
@else
<div class="cards-grid">
    @foreach($rateCards as $card)
    <div class="rate-card">
        <div class="rc-header">
            <div>
                <div class="rc-name">{{ $card->name }}</div>
                @if($card->description)
                    <div class="rc-desc">{{ Str::limit($card->description, 80) }}</div>
                @endif
            </div>
            @if($card->is_default)
                <span class="badge-default">★ Default</span>
            @endif
        </div>

        <div class="rc-stats">
            <div class="rc-stat">
                <div class="rc-stat-val">{{ $card->clients_count }}</div>
                <div class="rc-stat-lbl">Clients Assigned</div>
            </div>
        </div>

        <div class="rc-meta">
            Created by {{ $card->createdBy?->name ?? 'System' }}
            &bull; {{ $card->created_at->format('d M Y') }}
        </div>

        <div class="rc-actions">
            <a href="{{ route('admin.rates.show', $card) }}" class="btn btn-secondary btn-sm">👁 View</a>
            <a href="{{ route('admin.rates.edit', $card) }}" class="btn btn-secondary btn-sm">✏️ Edit</a>

            {{-- Duplicate --}}
            <form method="POST" action="{{ route('admin.rates.duplicate', $card) }}">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm"
                        onclick="return confirm('Duplicate this rate card with all its matrix rows?')">
                    ⎘ Duplicate
                </button>
            </form>

            {{-- Delete --}}
            @if(!$card->is_default && $card->clients_count === 0)
            <form method="POST" action="{{ route('admin.rates.destroy', $card) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Delete this rate card permanently? This cannot be undone.')">
                    🗑 Delete
                </button>
            </form>
            @else
            <span class="btn btn-sm" style="color:#cbd5e1;cursor:not-allowed;border:1px solid #f1f5f9"
                  title="{{ $card->is_default ? 'Cannot delete the default card' : 'Unassign all clients first' }}">
                🗑 Delete
            </span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
