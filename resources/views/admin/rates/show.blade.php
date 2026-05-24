@extends('admin.layouts.app')

@section('page-title')
    📋 {{ $rateCard->name }}
    @if($rateCard->is_default)
        <span style="font-size:11px;background:linear-gradient(135deg,#fbbf24,#f59e0b);color:#fff;padding:3px 9px;border-radius:20px;font-weight:700;margin-left:8px">★ DEFAULT</span>
    @endif
@endsection

@push('styles')
<style>
    /* Page actions */
    .page-actions { display:flex; gap:10px; justify-content:flex-end; margin-bottom:20px; flex-wrap:wrap; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:opacity 0.15s,transform 0.1s; white-space:nowrap; }
    .btn:active { transform:scale(0.98); }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { opacity:0.88; }
    .btn-outline { background:#fff; color:#475569; border:1.5px solid #e2e8f0; }
    .btn-outline:hover { background:#f8fafc; }
    .btn-danger { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
    .btn-danger:hover { background:#fee2e2; }
    .btn-warning { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
    .btn-sm { padding:5px 11px; font-size:12px; border-radius:7px; }
    .btn-success { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }

    /* Tabs */
    .tab-bar { display:flex; gap:2px; background:#f1f5f9; border-radius:12px; padding:4px; margin-bottom:22px; width:fit-content; }
    .tab-btn {
        padding:9px 20px; border-radius:9px; font-size:13px; font-weight:600;
        cursor:pointer; border:none; background:transparent; color:#64748b;
        transition:all 0.15s;
    }
    .tab-btn.active { background:#fff; color:#1e293b; box-shadow:0 1px 4px rgba(0,0,0,0.1); }
    .tab-btn:hover:not(.active) { color:#334155; background:rgba(255,255,255,0.5); }

    /* Cards */
    .panel { background:#fff; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.04); overflow:hidden; }
    .panel-header { padding:16px 20px; border-bottom:1px solid #f1f5f9; font-size:13px; font-weight:700; color:#64748b; letter-spacing:0.05em; text-transform:uppercase; display:flex; align-items:center; justify-content:space-between; }
    .panel-body { padding:20px; }

    /* Add row form */
    .add-row-form { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:18px; margin-bottom:20px; }
    .form-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:10px; align-items:end; }
    .form-group { }
    .form-label { display:block; font-size:11.5px; font-weight:700; color:#64748b; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:5px; }
    .form-control { width:100%; padding:8px 11px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; color:#0f172a; background:#fff; outline:none; transition:border-color 0.2s; }
    .form-control:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.1); }

    /* Matrix table */
    .matrix-section { margin-bottom:28px; }
    .service-title { font-size:13px; font-weight:700; color:#1e293b; background:#f8fafc; padding:10px 16px; border-radius:8px; margin-bottom:10px; display:flex; align-items:center; gap:8px; }
    table { width:100%; border-collapse:collapse; }
    thead th { background:#f8fafc; padding:10px 14px; text-align:left; font-size:11px; font-weight:700; color:#64748b; letter-spacing:0.06em; text-transform:uppercase; border-bottom:1px solid #e2e8f0; white-space:nowrap; }
    tbody td { padding:10px 14px; font-size:13px; color:#1e293b; border-bottom:1px solid #f8fafc; vertical-align:middle; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover { background:#fafcff; }
    .rate-cell { font-weight:700; color:#1e293b; }
    .rate-dash { color:#d1d5db; }

    /* Inline edit */
    .inline-edit-form { background:#eff6ff; border:1px solid #bfdbfe; border-radius:8px; padding:12px; margin-top:8px; }
    .inline-edit-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; margin-bottom:10px; }

    /* Intl table */
    .badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
    .badge-blue   { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .badge-green  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
    .badge-gray   { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }
    .badge-purple { background:#faf5ff; color:#7c3aed; border:1px solid #ddd6fe; }

    /* Clients tab */
    .client-row { display:flex; align-items:center; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f1f5f9; }
    .client-row:last-child { border-bottom:none; }
    .client-name { font-weight:600; color:#0f172a; font-size:13.5px; }
    .client-meta { font-size:12px; color:#64748b; margin-top:2px; }

    /* Meta footer */
    .meta-row { display:flex; gap:30px; flex-wrap:wrap; padding-top:18px; margin-top:18px; border-top:1px solid #f1f5f9; font-size:12.5px; color:#94a3b8; }
    .meta-item strong { color:#475569; display:block; font-size:11px; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:2px; }

    .empty-msg { padding:32px; text-align:center; color:#94a3b8; font-size:13px; }
    [x-cloak] { display:none !important; }
</style>
@endpush

@section('content')
{{-- Page actions --}}
<div class="page-actions">
    <a href="{{ route('admin.rates.index') }}" class="btn btn-outline">← All Rate Cards</a>
    <a href="{{ route('admin.rates.edit', $rateCard) }}" class="btn btn-primary">✏️ Edit Card</a>
    <form method="POST" action="{{ route('admin.rates.duplicate', $rateCard) }}" style="display:inline">
        @csrf
        <button type="submit" class="btn btn-warning" onclick="return confirm('Duplicate this card with all rates?')">⎘ Duplicate</button>
    </form>
</div>

{{-- Tab interface --}}
<div x-data="{ tab: 'matrix' }">

    <div class="tab-bar">
        <button class="tab-btn" :class="{ active: tab === 'matrix' }" @click="tab = 'matrix'">
            📊 Domestic Matrix
            <span style="background:#e2e8f0;border-radius:20px;padding:1px 7px;font-size:11px;margin-left:4px">
                {{ $rateCard->rateMatrix->count() }}
            </span>
        </button>
        <button class="tab-btn" :class="{ active: tab === 'intl' }" @click="tab = 'intl'">
            🌍 International
            <span style="background:#e2e8f0;border-radius:20px;padding:1px 7px;font-size:11px;margin-left:4px">
                {{ $internationalRates->count() }}
            </span>
        </button>
        <button class="tab-btn" :class="{ active: tab === 'clients' }" @click="tab = 'clients'">
            👥 Assigned Clients
            <span style="background:#e2e8f0;border-radius:20px;padding:1px 7px;font-size:11px;margin-left:4px">
                {{ $rateCard->clients->count() }}
            </span>
        </button>
    </div>

    {{-- ══════════════════ TAB 1: DOMESTIC MATRIX ══════════════════ --}}
    <div x-show="tab === 'matrix'" x-cloak>

        {{-- Add Row Form --}}
        <div x-data="{ showForm: false }">
            <div style="margin-bottom:14px">
                <button @click="showForm = !showForm" class="btn btn-success">
                    <span x-text="showForm ? '✕ Close' : '+ Add Rate Row'"></span>
                </button>
            </div>

            <div x-show="showForm" x-cloak class="add-row-form">
                <form method="POST" action="{{ route('admin.rates.matrix.store', $rateCard) }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Service Type</label>
                            <select name="service_type" class="form-control" required>
                                @foreach($domesticServices as $svc)
                                    <option value="{{ $svc }}" {{ old('service_type') === $svc ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucwords($svc)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Weight From (kg)</label>
                            <input type="number" name="weight_from" step="0.001" min="0"
                                   class="form-control" placeholder="0" value="{{ old('weight_from') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Weight To (kg)</label>
                            <input type="number" name="weight_to" step="0.001" min="0"
                                   class="form-control" placeholder="0.5" value="{{ old('weight_to') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Zone</label>
                            <select name="zone_code" class="form-control" required>
                                @foreach($zones as $z)
                                    <option value="{{ $z }}" {{ old('zone_code') === $z ? 'selected' : '' }}>Zone {{ $z }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Base Rate (₹)</label>
                            <input type="number" name="base_rate" step="0.01" min="0"
                                   class="form-control" placeholder="0.00" value="{{ old('base_rate') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fuel %</label>
                            <input type="number" name="fuel_surcharge_pct" step="0.01" min="0" max="100"
                                   class="form-control" placeholder="0" value="{{ old('fuel_surcharge_pct') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ODA Flat (₹)</label>
                            <input type="number" name="oda_flat" step="0.01" min="0"
                                   class="form-control" placeholder="0" value="{{ old('oda_flat') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">COD %</label>
                            <input type="number" name="cod_pct" step="0.01" min="0" max="100"
                                   class="form-control" placeholder="0" value="{{ old('cod_pct') }}">
                        </div>
                        <div class="form-group" style="display:flex;align-items:flex-end">
                            <button type="submit" class="btn btn-primary" style="width:100%">Add Row</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Matrix grouped by service --}}
        @forelse($domesticServices as $svc)
            @php $svcRows = $matrix[$svc] ?? []; @endphp
            <div class="panel matrix-section">
                <div class="panel-header">
                    <span>{{ str_replace('_',' ', ucwords($svc)) }}</span>
                    <span style="font-size:12px;color:#94a3b8">{{ count($svcRows) }} slabs configured</span>
                </div>
                @if(empty($svcRows))
                    <div class="empty-msg">No rates added yet for {{ str_replace('_',' ', ucwords($svc)) }}.</div>
                @else
                <div style="overflow-x:auto">
                <table>
                    <thead>
                        <tr>
                            <th>Weight Slab (kg)</th>
                            @foreach($zones as $z)
                                <th>Zone {{ $z }}</th>
                            @endforeach
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($weightSlabs as [$from, $to])
                        @php
                            $slabKey = $from . '-' . $to;
                            $hasAny  = false;
                            foreach ($zones as $z) {
                                if (isset($svcRows[$slabKey][$z])) { $hasAny = true; break; }
                            }
                        @endphp
                        @if($hasAny)
                        <tr x-data="{ editing: false }">
                            <td style="font-weight:600;white-space:nowrap">{{ $from }} – {{ $to == 999 ? '20+' : $to }}</td>
                            @foreach($zones as $z)
                                <td>
                                    @if(isset($svcRows[$slabKey][$z]))
                                        @php $row = $svcRows[$slabKey][$z]; @endphp
                                        <div class="rate-cell">₹{{ number_format($row->base_rate, 2) }}</div>
                                        <div style="font-size:11px;color:#94a3b8">F:{{ $row->fuel_surcharge_pct }}% ODA:₹{{ $row->oda_flat }} COD:{{ $row->cod_pct }}%</div>
                                    @else
                                        <span class="rate-dash">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                @php $anyRow = collect($zones)->map(fn($z) => $svcRows[$slabKey][$z] ?? null)->first(fn($r) => $r !== null); @endphp
                                @if($anyRow)
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <button @click="editing = !editing" class="btn btn-outline btn-sm">✏️</button>
                                    <form method="POST" action="{{ route('admin.rates.matrix.destroy', [$rateCard, $anyRow]) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Delete all rates for this weight slab?')">🗑</button>
                                    </form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @if($anyRow)
                        {{-- Inline edit row --}}
                        <tr x-show="editing" x-cloak style="background:#f0f9ff">
                            <td colspan="{{ count($zones) + 2 }}" style="padding:12px 14px">
                                <div class="inline-edit-form">
                                    <div style="font-size:12px;font-weight:700;color:#1d4ed8;margin-bottom:10px">
                                        Edit row: {{ $from }}–{{ $to == 999 ? '20+' : $to }} kg / Zone {{ $anyRow->zone_code }}
                                    </div>
                                    <form method="POST" action="{{ route('admin.rates.matrix.update', [$rateCard, $anyRow]) }}">
                                        @csrf @method('PUT')
                                        <div class="inline-edit-grid">
                                            <div>
                                                <div class="form-label">Base Rate (₹)</div>
                                                <input type="number" name="base_rate" step="0.01"
                                                       value="{{ $anyRow->base_rate }}" class="form-control" required>
                                            </div>
                                            <div>
                                                <div class="form-label">Fuel %</div>
                                                <input type="number" name="fuel_surcharge_pct" step="0.01"
                                                       value="{{ $anyRow->fuel_surcharge_pct }}" class="form-control">
                                            </div>
                                            <div>
                                                <div class="form-label">ODA Flat (₹)</div>
                                                <input type="number" name="oda_flat" step="0.01"
                                                       value="{{ $anyRow->oda_flat }}" class="form-control">
                                            </div>
                                            <div>
                                                <div class="form-label">COD %</div>
                                                <input type="number" name="cod_pct" step="0.01"
                                                       value="{{ $anyRow->cod_pct }}" class="form-control">
                                            </div>
                                        </div>
                                        <div style="display:flex;gap:8px">
                                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                            <button type="button" @click="editing = false" class="btn btn-outline btn-sm">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endif
                    @endforeach
                    </tbody>
                </table>
                </div>
                @endif
            </div>
        @empty
        @endforelse
    </div>

    {{-- ══════════════════ TAB 2: INTERNATIONAL ══════════════════ --}}
    <div x-show="tab === 'intl'" x-cloak>

        {{-- Add international rate --}}
        <div x-data="{ showIntl: false }" style="margin-bottom:16px">
            <button @click="showIntl = !showIntl" class="btn btn-success" style="margin-bottom:14px">
                <span x-text="showIntl ? '✕ Close' : '+ Add International Rate'"></span>
            </button>

            <div x-show="showIntl" x-cloak class="add-row-form">
                <form method="POST" action="{{ route('admin.rates.international.store', $rateCard) }}">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Country Group</label>
                            <select name="country_group" class="form-control" required>
                                @foreach($countryGroups as $cg)
                                    <option value="{{ $cg }}" {{ old('country_group') === $cg ? 'selected' : '' }}>{{ $cg }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Service</label>
                            <select name="service_type" class="form-control" required>
                                @foreach($intlServices as $s)
                                    <option value="{{ $s }}" {{ old('service_type') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Weight From</label>
                            <input type="number" name="weight_from" step="0.001" min="0" class="form-control" placeholder="0" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Weight To</label>
                            <input type="number" name="weight_to" step="0.001" min="0" class="form-control" placeholder="0.5" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Base Rate (₹)</label>
                            <input type="number" name="base_rate" step="0.01" min="0" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fuel %</label>
                            <input type="number" name="fuel_surcharge_pct" step="0.01" min="0" class="form-control" placeholder="0">
                        </div>
                        <div class="form-group" style="display:flex;align-items:flex-end">
                            <button type="submit" class="btn btn-primary" style="width:100%">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel">
            @if($internationalRates->isEmpty())
                <div class="empty-msg">🌍 No international rates configured yet.</div>
            @else
            <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>Country Group</th>
                        <th>Service</th>
                        <th>Weight Range (kg)</th>
                        <th>Base Rate</th>
                        <th>Fuel %</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($internationalRates as $intl)
                <tr>
                    <td><span class="badge badge-purple">{{ $intl->country_group }}</span></td>
                    <td><span class="badge badge-blue">{{ ucfirst($intl->service_type) }}</span></td>
                    <td style="font-family:monospace">{{ $intl->weight_from }} – {{ $intl->weight_to }}</td>
                    <td style="font-weight:700">₹{{ number_format($intl->base_rate, 2) }}</td>
                    <td>{{ $intl->fuel_surcharge_pct }}%</td>
                    <td>
                        <form method="POST" action="{{ route('admin.rates.international.destroy', [$rateCard, $intl]) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this international rate?')">🗑 Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════ TAB 3: ASSIGNED CLIENTS ══════════════════ --}}
    <div x-show="tab === 'clients'" x-cloak>

        {{-- Assign form --}}
        @if($availableClients->isNotEmpty())
        <div class="panel" style="margin-bottom:16px">
            <div class="panel-header">🔗 Assign a Client to This Rate Card</div>
            <div class="panel-body">
                <form method="POST" action="{{ route('admin.rates.assign') }}">
                    @csrf
                    <input type="hidden" name="rate_card_id" value="{{ $rateCard->id }}">
                    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
                        <div style="flex:1;min-width:200px">
                            <label class="form-label">Select Client</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">— Choose a client —</option>
                                @foreach($availableClients as $c)
                                    <option value="{{ $c->id }}">{{ $c->company_name }} ({{ $c->user?->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Assigned clients list --}}
        <div class="panel">
            <div class="panel-header">
                <span>Clients on this Rate Card</span>
                <span style="font-size:12px;color:#94a3b8">{{ $rateCard->clients->count() }} assigned</span>
            </div>
            @if($rateCard->clients->isEmpty())
                <div class="empty-msg">👥 No clients assigned to this rate card yet.</div>
            @else
            <div class="panel-body">
                @foreach($rateCard->clients as $c)
                <div class="client-row">
                    <div>
                        <div class="client-name">{{ $c->company_name }}</div>
                        <div class="client-meta">{{ $c->user?->email }} &bull; {{ $c->city }}, {{ $c->state }}
                            &bull; <span class="badge {{ $c->account_type === 'credit' ? 'badge-blue' : 'badge-green' }}" style="font-size:10px">{{ ucfirst($c->account_type) }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.clients.show', $c) }}" class="btn btn-outline btn-sm">View →</a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

</div>{{-- end x-data tab --}}

{{-- Card metadata --}}
<div class="meta-row" style="margin-top:24px">
    <div class="meta-item">
        <strong>Created By</strong>
        {{ $rateCard->createdBy?->name ?? 'System' }}
    </div>
    <div class="meta-item">
        <strong>Created At</strong>
        {{ $rateCard->created_at->format('d M Y, h:i A') }}
    </div>
    @if($rateCard->updatedBy)
    <div class="meta-item">
        <strong>Last Updated By</strong>
        {{ $rateCard->updatedBy->name }}
    </div>
    @endif
    <div class="meta-item">
        <strong>Last Updated</strong>
        {{ $rateCard->updated_at->format('d M Y, h:i A') }}
    </div>
</div>

@endsection
