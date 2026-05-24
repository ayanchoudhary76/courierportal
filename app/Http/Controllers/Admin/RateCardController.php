<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\InternationalRate;
use App\Models\RateCard;
use App\Models\RateMatrix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RateCardController extends Controller
{
    // ─── Constants ────────────────────────────────────────────────────
    const ZONES = ['A', 'B', 'C', 'D', 'E'];

    const ZONE_LABELS = [
        'A' => 'Zone A — Metro (up to 100km)',
        'B' => 'Zone B — Local (101–300km)',
        'C' => 'Zone C — Regional (301–600km)',
        'D' => 'Zone D — National (601–1500km)',
        'E' => 'Zone E — Far National / ODA (1500km+)',
    ];

    const DOMESTIC_SERVICES = ['express_air', 'priority_surface', 'economy_surface'];

    const WEIGHT_SLABS = [
        [0, 0.5], [0.5, 1], [1, 2], [2, 3], [3, 5],
        [5, 7],   [7, 10],  [10, 15], [15, 20], [20, 999],
    ];

    const COUNTRY_GROUPS = [
        'SAARC', 'Southeast Asia', 'Middle East', 'Europe',
        'North America', 'Australia/NZ', 'Rest of World',
    ];

    const INTL_SERVICES = ['express', 'economy'];

    // ─── Helpers ──────────────────────────────────────────────────────

    private function constants(): array
    {
        return [
            'zones'            => self::ZONES,
            'zoneLabels'       => self::ZONE_LABELS,
            'domesticServices' => self::DOMESTIC_SERVICES,
            'weightSlabs'      => self::WEIGHT_SLABS,
            'countryGroups'    => self::COUNTRY_GROUPS,
            'intlServices'     => self::INTL_SERVICES,
        ];
    }

    private function buildMatrix(RateCard $rateCard): array
    {
        $matrix = [];
        foreach ($rateCard->rateMatrix as $row) {
            $slabKey = $row->weight_from . '-' . $row->weight_to;
            $matrix[$row->service_type][$slabKey][$row->zone_code] = $row;
        }
        return $matrix;
    }

    // ─── INDEX ────────────────────────────────────────────────────────
    public function index()
    {
        $rateCards = RateCard::withCount('clients')
            ->with('createdBy')
            ->latest()
            ->get();

        return view('admin.rates.index', compact('rateCards'));
    }

    // ─── CREATE ───────────────────────────────────────────────────────
    public function create()
    {
        $clients = Client::with('user')->where('is_active', true)->get();

        return view('admin.rates.create', array_merge(
            $this->constants(),
            compact('clients')
        ));
    }

    // ─── STORE ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_default'  => ['nullable', 'boolean'],
        ]);

        $rateCard = DB::transaction(function () use ($validated) {
            if (! empty($validated['is_default'])) {
                RateCard::where('is_default', true)->update(['is_default' => false]);
            }

            $card = RateCard::create([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_default'  => ! empty($validated['is_default']),
                'created_by'  => Auth::id(),
            ]);

            AuditHelper::log('create', 'rate_cards', $card->id, [], $card->toArray());

            return $card;
        });

        return redirect()->route('admin.rates.show', $rateCard)
            ->with('success', 'Rate card created successfully. Now add rate matrix rows.');
    }

    // ─── SHOW ─────────────────────────────────────────────────────────
    public function show(RateCard $rateCard)
    {
        $rateCard->load([
            'rateMatrix',
            'internationalRates',
            'clients.user',
            'createdBy',
            'updatedBy',
        ]);

        $matrix             = $this->buildMatrix($rateCard);
        $internationalRates = $rateCard->internationalRates;

        // All active clients (for assign dropdown — exclude already-assigned)
        $assignedIds    = $rateCard->clients->pluck('id')->toArray();
        $availableClients = Client::with('user')
            ->where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->get();

        return view('admin.rates.show', array_merge(
            $this->constants(),
            compact('rateCard', 'matrix', 'internationalRates', 'availableClients')
        ));
    }

    // ─── EDIT ─────────────────────────────────────────────────────────
    public function edit(RateCard $rateCard)
    {
        $clients = Client::with('user')->where('is_active', true)->get();

        return view('admin.rates.edit', compact('rateCard', 'clients'));
    }

    // ─── UPDATE ───────────────────────────────────────────────────────
    public function update(Request $request, RateCard $rateCard)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_default'  => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated, $rateCard) {
            $old = $rateCard->toArray();

            if (! empty($validated['is_default'])) {
                RateCard::where('is_default', true)
                    ->where('id', '!=', $rateCard->id)
                    ->update(['is_default' => false]);
            }

            $rateCard->update([
                'name'        => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_default'  => ! empty($validated['is_default']),
                'updated_by'  => Auth::id(),
            ]);

            AuditHelper::log('update', 'rate_cards', $rateCard->id, $old, $rateCard->fresh()->toArray());
        });

        return redirect()->route('admin.rates.show', $rateCard)
            ->with('success', 'Rate card updated successfully.');
    }

    // ─── DESTROY ──────────────────────────────────────────────────────
    public function destroy(RateCard $rateCard)
    {
        if ($rateCard->is_default) {
            return back()->with('error', 'Cannot delete the default rate card. Remove the default flag first.');
        }

        if ($rateCard->clients()->count() > 0) {
            return back()->with('error', 'Cannot delete this rate card because clients are assigned to it.');
        }

        DB::transaction(function () use ($rateCard) {
            AuditHelper::log('delete', 'rate_cards', $rateCard->id, $rateCard->toArray(), []);
            $rateCard->rateMatrix()->delete();
            $rateCard->internationalRates()->delete();
            $rateCard->delete();
        });

        return redirect()->route('admin.rates.index')
            ->with('success', 'Rate card deleted successfully.');
    }

    // ─── STORE MATRIX ─────────────────────────────────────────────────
    public function storeMatrix(Request $request, RateCard $rateCard)
    {
        $validated = $request->validate([
            'service_type'       => ['required', 'in:' . implode(',', self::DOMESTIC_SERVICES)],
            'weight_from'        => ['required', 'numeric', 'min:0'],
            'weight_to'          => ['required', 'numeric', 'gt:weight_from'],
            'zone_code'          => ['required', 'in:' . implode(',', self::ZONES)],
            'base_rate'          => ['required', 'numeric', 'min:0'],
            'fuel_surcharge_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'oda_flat'           => ['nullable', 'numeric', 'min:0'],
            'cod_pct'            => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        // Uniqueness check
        $exists = RateMatrix::where('rate_card_id', $rateCard->id)
            ->where('service_type', $validated['service_type'])
            ->where('weight_from', $validated['weight_from'])
            ->where('weight_to', $validated['weight_to'])
            ->where('zone_code', $validated['zone_code'])
            ->exists();

        if ($exists) {
            $msg = 'A rate already exists for this combination. Edit the existing row instead.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $row = RateMatrix::create(array_merge(
            ['rate_card_id' => $rateCard->id],
            [
                'service_type'       => $validated['service_type'],
                'weight_from'        => $validated['weight_from'],
                'weight_to'          => $validated['weight_to'],
                'zone_code'          => $validated['zone_code'],
                'base_rate'          => $validated['base_rate'],
                'fuel_surcharge_pct' => $validated['fuel_surcharge_pct'] ?? 0,
                'oda_flat'           => $validated['oda_flat'] ?? 0,
                'cod_pct'            => $validated['cod_pct'] ?? 0,
            ]
        ));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'matrix' => $row]);
        }

        return back()->with('success', 'Rate row added.');
    }

    // ─── UPDATE MATRIX ────────────────────────────────────────────────
    public function updateMatrix(Request $request, RateCard $rateCard, RateMatrix $matrix)
    {
        $validated = $request->validate([
            'base_rate'          => ['required', 'numeric', 'min:0'],
            'fuel_surcharge_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'oda_flat'           => ['nullable', 'numeric', 'min:0'],
            'cod_pct'            => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $matrix->update([
            'base_rate'          => $validated['base_rate'],
            'fuel_surcharge_pct' => $validated['fuel_surcharge_pct'] ?? $matrix->fuel_surcharge_pct,
            'oda_flat'           => $validated['oda_flat'] ?? $matrix->oda_flat,
            'cod_pct'            => $validated['cod_pct'] ?? $matrix->cod_pct,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'matrix' => $matrix->fresh()]);
        }

        return back()->with('success', 'Rate updated.');
    }

    // ─── DESTROY MATRIX ───────────────────────────────────────────────
    public function destroyMatrix(RateCard $rateCard, RateMatrix $matrix)
    {
        $matrix->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Rate row deleted.');
    }

    // ─── STORE INTERNATIONAL ──────────────────────────────────────────
    public function storeInternational(Request $request, RateCard $rateCard)
    {
        $validated = $request->validate([
            'country_group'      => ['required', 'in:' . implode(',', self::COUNTRY_GROUPS)],
            'service_type'       => ['required', 'in:' . implode(',', self::INTL_SERVICES)],
            'weight_from'        => ['required', 'numeric', 'min:0'],
            'weight_to'          => ['required', 'numeric', 'gt:weight_from'],
            'base_rate'          => ['required', 'numeric', 'min:0'],
            'fuel_surcharge_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        InternationalRate::create(array_merge(
            ['rate_card_id' => $rateCard->id],
            [
                'country_group'      => $validated['country_group'],
                'service_type'       => $validated['service_type'],
                'weight_from'        => $validated['weight_from'],
                'weight_to'          => $validated['weight_to'],
                'base_rate'          => $validated['base_rate'],
                'fuel_surcharge_pct' => $validated['fuel_surcharge_pct'] ?? 0,
            ]
        ));

        return back()->with('success', 'International rate added successfully.');
    }

    // ─── DESTROY INTERNATIONAL ────────────────────────────────────────
    public function destroyInternational(RateCard $rateCard, InternationalRate $rate)
    {
        $rate->delete();
        return back()->with('success', 'International rate deleted.');
    }

    // ─── ASSIGN TO CLIENT ─────────────────────────────────────────────
    public function assignToClient(Request $request)
    {
        $validated = $request->validate([
            'client_id'    => ['required', 'exists:clients,id'],
            'rate_card_id' => ['required', 'exists:rate_cards,id'],
        ]);

        $client = Client::findOrFail($validated['client_id']);
        $old    = ['rate_card_id' => $client->rate_card_id];
        $client->update(['rate_card_id' => $validated['rate_card_id']]);

        AuditHelper::log(
            'assign_rate_card',
            'clients',
            $client->id,
            $old,
            ['rate_card_id' => $validated['rate_card_id']]
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Rate card assigned successfully.']);
        }

        return back()->with('success', 'Rate card assigned to client.');
    }

    // ─── DUPLICATE ────────────────────────────────────────────────────
    public function duplicate(RateCard $rateCard)
    {
        $newCard = DB::transaction(function () use ($rateCard) {
            $new = RateCard::create([
                'name'        => 'Copy of ' . $rateCard->name,
                'description' => $rateCard->description,
                'is_default'  => false,
                'created_by'  => Auth::id(),
            ]);

            // Clone matrix rows
            foreach ($rateCard->rateMatrix as $row) {
                RateMatrix::create(array_merge(
                    $row->only([
                        'service_type', 'weight_from', 'weight_to', 'zone_code',
                        'base_rate', 'fuel_surcharge_pct', 'oda_flat', 'cod_pct',
                    ]),
                    ['rate_card_id' => $new->id]
                ));
            }

            // Clone international rates
            foreach ($rateCard->internationalRates as $intl) {
                InternationalRate::create(array_merge(
                    $intl->only([
                        'country_group', 'service_type', 'weight_from', 'weight_to',
                        'base_rate', 'fuel_surcharge_pct',
                    ]),
                    ['rate_card_id' => $new->id]
                ));
            }

            AuditHelper::log('duplicate', 'rate_cards', $new->id, [], $new->toArray());

            return $new;
        });

        return redirect()->route('admin.rates.show', $newCard)
            ->with('success', 'Rate card duplicated successfully.');
    }
}
