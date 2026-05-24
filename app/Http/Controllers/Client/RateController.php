<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\InternationalRate;
use App\Models\RateCard;
use App\Models\RateMatrix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    // ─── Page ─────────────────────────────────────────────────────────
    public function index()
    {
        $client     = Auth::user()->client()->with('rateCard')->first();
        $rateCardId = $client?->rate_card_id ?? RateCard::where('is_default', true)->value('id');

        return view('client.rates', [
            'client'      => $client,
            'rateCardId'  => $rateCardId,
            'hasRateCard' => (bool) $rateCardId,
        ]);
    }

    // ─── AJAX calculate ───────────────────────────────────────────────
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'type'               => ['required', 'in:domestic,international'],
            'origin_pincode'     => ['required_if:type,domestic', 'nullable', 'digits:6'],
            'dest_pincode'       => ['required_if:type,domestic', 'nullable', 'digits:6'],
            'origin_city'        => ['required_if:type,international', 'nullable', 'string'],
            'dest_country_group' => ['required_if:type,international', 'nullable', 'string'],
            'weight_actual'      => ['required', 'numeric', 'min:0.1', 'max:999'],
            'length'             => ['nullable', 'numeric', 'min:1'],
            'width'              => ['nullable', 'numeric', 'min:1'],
            'height'             => ['nullable', 'numeric', 'min:1'],
            'service_type'       => ['required', 'string'],
            'parcel_type'        => ['required', 'in:document,non_document,fragile'],
        ]);

        // Dimensional weight
        $dimWeight = 0;
        if ($request->length && $request->width && $request->height) {
            $divisor   = in_array($request->service_type, ['express_air', 'international_express']) ? 5000 : 4000;
            $dimWeight = ($request->length * $request->width * $request->height) / $divisor;
        }
        $chargeableWeight = (float) max($request->weight_actual, $dimWeight);

        // Rate card
        $client     = Auth::user()->client;
        $rateCardId = $client?->rate_card_id ?? RateCard::where('is_default', true)->value('id');

        if (! $rateCardId) {
            return response()->json(['success' => false, 'message' => 'No rate card assigned. Please contact support.']);
        }

        $zone       = null;
        $baseRate   = null;
        $fuelPct    = 0;
        $odaCharge  = 0;

        if ($request->type === 'domestic') {
            $zone   = $this->getZoneFromPincodes($request->origin_pincode, $request->dest_pincode);
            $matrix = RateMatrix::where('rate_card_id', $rateCardId)
                ->where('service_type', $request->service_type)
                ->where('zone_code', $zone)
                ->where('weight_from', '<=', $chargeableWeight)
                ->where('weight_to',   '>=', $chargeableWeight)
                ->first();

            if (! $matrix) {
                return response()->json(['success' => false, 'message' => 'No rate available for this combination. Please contact us.']);
            }

            $baseRate  = (float) $matrix->base_rate;
            $fuelPct   = (float) $matrix->fuel_surcharge_pct;
            $odaCharge = $this->isOdaPincode($request->dest_pincode) ? (float) $matrix->oda_flat : 0;

        } else {
            $intlRate = InternationalRate::where('rate_card_id', $rateCardId)
                ->where('country_group',  $request->dest_country_group)
                ->where('service_type',   $request->service_type)
                ->where('weight_from',    '<=', $chargeableWeight)
                ->where('weight_to',      '>=', $chargeableWeight)
                ->first();

            if (! $intlRate) {
                return response()->json(['success' => false, 'message' => 'No international rate available for this combination.']);
            }

            $baseRate = (float) $intlRate->base_rate;
            $fuelPct  = (float) $intlRate->fuel_surcharge_pct;
        }

        $fuelCharge = round($baseRate * $fuelPct / 100, 2);
        $subtotal   = $baseRate + $fuelCharge + $odaCharge;
        $gst        = round($subtotal * 0.18, 2);
        $total      = round($subtotal + $gst, 2);

        return response()->json([
            'success'   => true,
            'breakdown' => [
                'chargeable_weight' => round($chargeableWeight, 3),
                'actual_weight'     => (float) $request->weight_actual,
                'dimensional_weight'=> round($dimWeight, 3),
                'zone'              => $zone,
                'base_freight'      => $baseRate,
                'fuel_surcharge'    => $fuelCharge,
                'fuel_pct'          => $fuelPct,
                'oda_charge'        => $odaCharge,
                'gst'               => $gst,
                'total'             => $total,
                'service_type'      => $request->service_type,
                'transit_days'      => $this->getTransitDays($request->service_type, $zone),
            ],
        ]);
    }

    // ─── Private helpers ──────────────────────────────────────────────

    private function getZoneFromPincodes(string $origin, string $dest): string
    {
        // Exact same first 3 digits → Metro (A)
        if (substr($origin, 0, 3) === substr($dest, 0, 3)) return 'A';

        $o1 = (int) $origin[0];
        $d1 = (int) $dest[0];
        $o3 = (int) substr($origin, 0, 3);
        $d3 = (int) substr($dest,   0, 3);

        // Same first digit, close range → Local (B)
        if ($o1 === $d1 && abs($o3 - $d3) <= 20) return 'B';

        // Same first digit → Regional (C)
        if ($o1 === $d1) return 'C';

        // NE / Andaman pincodes
        if ($o1 === 9 || $d1 === 9) return 'E';

        // Adjacent regions map
        $adjacent = [
            1 => [1, 2, 3],
            2 => [1, 2, 3, 4],
            3 => [1, 2, 3, 4],
            4 => [2, 3, 4, 5, 6],
            5 => [4, 5, 6],
            6 => [4, 5, 6, 7],
            7 => [6, 7, 8],
            8 => [7, 8],
        ];

        if (in_array($d1, $adjacent[$o1] ?? [])) return 'D';

        return 'D';
    }

    private function isOdaPincode(string $pincode): bool
    {
        return $pincode[0] === '9';
    }

    private function getTransitDays(string $serviceType, ?string $zone): string
    {
        return match ($serviceType) {
            'express_air'            => '1-2 business days',
            'priority_surface'       => '3-5 business days',
            'economy_surface'        => '5-7 business days',
            'international_express'  => '3-5 business days',
            'international_economy'  => '7-10 business days',
            default                  => '3-7 business days',
        };
    }
}
