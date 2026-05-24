@extends('admin.layouts.app')

@section('page-title')
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:#3b82f6">
        <circle cx="9" cy="7" r="4"/>
        <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        <path d="M21 21v-2a4 4 0 0 0-3-3.85"/>
    </svg>
    Client Management
@endsection

@push('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 16px;
        flex-wrap: wrap;
    }
    .search-form {
        display: flex;
        gap: 8px;
        flex: 1;
        max-width: 420px;
    }
    .search-input {
        flex: 1;
        padding: 9px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        font-size: 13.5px;
        outline: none;
        background: #fff;
        transition: border-color 0.2s;
    }
    .search-input:focus { border-color: #3b82f6; }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        border-radius: 9px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        text-decoration: none;
        transition: opacity 0.15s, transform 0.1s;
        white-space: nowrap;
    }
    .btn:active { transform: scale(0.98); }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { opacity: 0.88; }
    .btn-secondary { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .btn-danger:hover { background: #fee2e2; }
    .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 7px; }

    /* Table */
    .table-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .table-meta {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    table { width: 100%; border-collapse: collapse; }
    thead th {
        background: #f8fafc;
        padding: 12px 16px;
        text-align: left;
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    tbody td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover { background: #fafcff; }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }
    .badge-blue    { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .badge-orange  { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
    .badge-green   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-red     { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-gray    { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

    .actions { display: flex; gap: 6px; align-items: center; }

    /* Empty state */
    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #94a3b8;
    }
    .empty-state-icon { font-size: 42px; margin-bottom: 12px; }
    .empty-state-title { font-size: 15px; font-weight: 600; color: #475569; margin-bottom: 6px; }

    /* Client name cell */
    .client-name { font-weight: 600; color: #0f172a; }
    .client-email { font-size: 12px; color: #64748b; margin-top: 2px; }

    /* Pagination */
    .pagination-wrap { padding: 16px 20px; border-top: 1px solid #f1f5f9; }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="page-header">
    <form method="GET" action="{{ route('admin.clients.index') }}" class="search-form">
        <input
            type="text"
            name="search"
            class="search-input"
            placeholder="Search by company, email, phone, city…"
            value="{{ $search }}"
        >
        <button type="submit" class="btn btn-secondary btn-sm" style="padding:9px 14px">
            🔍 Search
        </button>
        @if($search)
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary btn-sm" style="padding:9px 14px">✕ Clear</a>
        @endif
    </form>

    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add New Client
    </a>
</div>

{{-- Table --}}
<div class="table-card">
    <div class="table-meta">
        <span>
            @if($search)
                Results for <strong>"{{ $search }}"</strong> &mdash;
            @endif
            <strong>{{ $clients->total() }}</strong> client{{ $clients->total() !== 1 ? 's' : '' }} found
        </span>
        <span>Page {{ $clients->currentPage() }} of {{ $clients->lastPage() }}</span>
    </div>

    @if($clients->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <div class="empty-state-title">No clients found</div>
            <p>{{ $search ? 'Try a different search term.' : 'Add your first client using the button above.' }}</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Company / Contact</th>
                    <th>City</th>
                    <th>Account</th>
                    <th>Rate Card</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td style="color:#94a3b8;font-size:12px">#{{ $client->id }}</td>
                    <td>
                        <div class="client-name">{{ $client->company_name }}</div>
                        <div class="client-email">{{ $client->user?->name }} &bull; {{ $client->user?->email }}</div>
                    </td>
                    <td>{{ $client->city }}, {{ $client->state }}</td>
                    <td>
                        @if($client->account_type === 'credit')
                            <span class="badge badge-orange">Credit</span>
                        @else
                            <span class="badge badge-blue">Prepaid</span>
                        @endif
                    </td>
                    <td>{{ $client->rateCard?->name ?? '—' }}</td>
                    <td>
                        @if($client->is_active)
                            <span class="badge badge-green">● Active</span>
                        @else
                            <span class="badge badge-red">● Suspended</span>
                        @endif
                    </td>
                    <td style="color:#64748b;font-size:12.5px">{{ $client->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-secondary btn-sm">View</a>
                            <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                            @if($client->is_active)
                            <form method="POST" action="{{ route('admin.clients.destroy', $client->id) }}"
                                  onsubmit="return confirm('Are you sure you want to suspend this client? Their account will be deactivated.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Suspend</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($clients->hasPages())
        <div class="pagination-wrap">
            {{ $clients->links() }}
        </div>
        @endif
    @endif
</div>

@endsection
