<?php

namespace App\Http\Controllers\Client;

use App\Exports\ClientBookingsExport;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\InternationalRate;
use App\Models\RateCard;
use App\Models\RateMatrix;
use App\Models\TrackingEvent;
use App\Services\AwbService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    // ─── List ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $client = Auth::user()->client;

        $query = $client
            ? $client->bookings()->latest()
            : Booking::whereNull('id');

        // Filters
        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('awb_number', 'like', "%{$s}%")
                  ->orWhere('receiver_name', 'like', "%{$s}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();

        return view('client.bookings', compact('bookings'));
    }

    // ─── Create wizard ────────────────────────────────────────────────
    public function create(Request $request)
    {
        $client     = Auth::user()->client()->with('rateCard', 'user')->first();
        $rateCardId = $client?->rate_card_id ?? RateCard::where('is_default', true)->value('id');

        $prefill = [
            'origin_pincode' => $request->query('origin_pincode', $client?->pincode),
            'dest_pincode'   => $request->query('dest_pincode'),
            'weight'         => $request->query('weight'),
            'service_type'   => $request->query('service_type', 'express_air'),
        ];

        return view('client.book', compact('client', 'rateCardId', 'prefill'));
    }

    // ─── Store ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name'            => ['required', 'string', 'max:100'],
            'sender_address'         => ['required', 'string'],
            'sender_pincode'         => ['required', 'digits:6'],
            'sender_phone'           => ['required', 'digits_between:10,15'],
            'receiver_name'          => ['required', 'string', 'max:100'],
            'receiver_address'       => ['required', 'string'],
            'receiver_pincode'       => ['required', 'digits:6'],
            'receiver_phone'         => ['required', 'digits_between:10,15'],
            'service_type'           => ['required', 'in:express_air,priority_surface,economy_surface,international_express,international_economy'],
            'parcel_type'            => ['required', 'in:document,non_document,fragile'],
            'weight_actual'          => ['required', 'numeric', 'min:0.1'],
            'length'                 => ['nullable', 'numeric', 'min:1'],
            'width'                  => ['nullable', 'numeric', 'min:1'],
            'height'                 => ['nullable', 'numeric', 'min:1'],
            'declared_value'         => ['nullable', 'numeric', 'min:0'],
            'pieces'                 => ['required', 'integer', 'min:1', 'max:100'],
            'pickup_type'            => ['required', 'in:door_pickup,drop_at_office'],
            'pickup_date'            => ['required_if:pickup_type,door_pickup', 'nullable', 'date', 'after:today'],
            'special_instructions'   => ['nullable', 'string', 'max:500'],
            'payment_mode'           => ['required', 'in:online,bill_to_account'],
        ]);

        $client     = Auth::user()->client;
        $rateCardId = $client?->rate_card_id ?? RateCard::where('is_default', true)->value('id');

        if (! $rateCardId) {
            return back()->withErrors(['rate' => 'No rate card assigned. Please contact support.'])->withInput();
        }

        // Dimensional weight
        $dimWeight = 0;
        if ($request->length && $request->width && $request->height) {
            $divisor   = in_array($request->service_type, ['express_air', 'international_express']) ? 5000 : 4000;
            $dimWeight = ($request->length * $request->width * $request->height) / $divisor;
        }
        $chargeableWeight = max((float) $request->weight_actual, $dimWeight);

        // Rate lookup
        $rateBreakdown = $this->recalculateRate($rateCardId, $request->service_type, $request->sender_pincode, $request->receiver_pincode, $chargeableWeight);

        if (! $rateBreakdown) {
            return back()->withErrors(['rate' => 'Rate not available for this route. Please use the calculator first.'])->withInput();
        }

        $booking = DB::transaction(function () use ($validated, $client, $dimWeight, $chargeableWeight, $rateBreakdown) {
            $awb = AwbService::generate();

            $booking = Booking::create([
                'awb_number'         => $awb,
                'client_id'          => $client->id,
                'sender_name'        => $validated['sender_name'],
                'sender_address'     => $validated['sender_address'],
                'sender_pincode'     => $validated['sender_pincode'],
                'sender_phone'       => $validated['sender_phone'],
                'receiver_name'      => $validated['receiver_name'],
                'receiver_address'   => $validated['receiver_address'],
                'receiver_pincode'   => $validated['receiver_pincode'],
                'receiver_phone'     => $validated['receiver_phone'],
                'service_type'       => $validated['service_type'],
                'parcel_type'        => $validated['parcel_type'],
                'weight_actual'      => $validated['weight_actual'],
                'weight_volumetric'  => round($dimWeight, 3),
                'declared_value'     => $validated['declared_value'] ?? 0,
                'pieces'             => $validated['pieces'],
                'base_amount'        => $rateBreakdown['base_freight'],
                'surcharges'         => [
                    'fuel_charge' => $rateBreakdown['fuel_surcharge'],
                    'fuel_pct'    => $rateBreakdown['fuel_pct'],
                    'oda_charge'  => $rateBreakdown['oda_charge'],
                    'gst'         => $rateBreakdown['gst'],
                    'zone'        => $rateBreakdown['zone'],
                ],
                'total_amount'       => $rateBreakdown['total'],
                'payment_mode'       => $validated['payment_mode'],
                'payment_status'     => 'pending',
                'booking_status'     => 'booked',
                'special_instructions' => $validated['special_instructions'] ?? null,
            ]);

            TrackingEvent::create([
                'booking_id' => $booking->id,
                'event_type' => 'booked',
                'location'   => 'Origin – ' . $validated['sender_pincode'],
                'remarks'    => 'Booking confirmed online. AWB: ' . $awb,
                'event_time' => now(),
                'created_by' => Auth::id(),
            ]);

            return $booking;
        });

        return redirect()
            ->route('client.bookings.show', $booking->awb_number)
            ->with('success', 'Booking confirmed! Your AWB number is ' . $booking->awb_number);
    }

    // ─── Show ─────────────────────────────────────────────────────────
    public function show(string $awb)
    {
        $client  = Auth::user()->client;
        $booking = Booking::where('awb_number', strtoupper($awb))
            ->where('client_id', $client->id)
            ->with(['trackingEvents' => fn ($q) => $q->orderBy('event_time')])
            ->firstOrFail();

        return view('client.booking-show', compact('booking'));
    }

    // ─── Label PDF ────────────────────────────────────────────────────
    public function downloadLabel(string $awb)
    {
        $client  = Auth::user()->client;
        $booking = Booking::where('awb_number', strtoupper($awb))
            ->where('client_id', $client->id)
            ->firstOrFail();

        $pdf = Pdf::loadView('client.label-pdf', ['booking' => $booking])
            ->setPaper([0, 0, 283.46, 425.2])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'dejavu sans',
                'dpi'                  => 96,
            ]);

        return $pdf->download('AWB-' . $booking->awb_number . '.pdf');
    }

    // ─── Export ───────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $client = Auth::user()->client;
        if (! $client) abort(403);

        $from = $request->date_from ?? now()->subDays(30)->format('Y-m-d');
        $to   = $request->date_to   ?? now()->format('Y-m-d');

        return Excel::download(
            new ClientBookingsExport($client->id, $from, $to),
            'my-bookings-' . $from . '-to-' . $to . '.xlsx'
        );
    }

    // ─── Re-book ──────────────────────────────────────────────────────
    public function rebook(string $awb)
    {
        $client  = Auth::user()->client;
        $original = Booking::where('awb_number', strtoupper($awb))
            ->where('client_id', $client->id)
            ->firstOrFail();

        $rateCardId = $client?->rate_card_id ?? RateCard::where('is_default', true)->value('id');

        $prefill = [
            'sender_name'      => $original->sender_name,
            'sender_address'   => $original->sender_address,
            'sender_pincode'   => $original->sender_pincode,
            'sender_phone'     => $original->sender_phone,
            'receiver_name'    => $original->receiver_name,
            'receiver_address' => $original->receiver_address,
            'receiver_pincode' => $original->receiver_pincode,
            'receiver_phone'   => $original->receiver_phone,
            'service_type'     => $original->service_type,
            'parcel_type'      => $original->parcel_type,
            'weight_actual'    => $original->weight_actual,
            'declared_value'   => $original->declared_value,
            'pieces'           => $original->pieces,
        ];

        return view('client.book', compact('client', 'rateCardId', 'prefill'));
    }

    // ─── Private rate helper ──────────────────────────────────────────
    private function recalculateRate(int $rateCardId, string $serviceType, string $originPin, string $destPin, float $chargeableWeight): ?array
    {
        $zone = $this->getZoneFromPincodes($originPin, $destPin);

        $matrix = RateMatrix::where('rate_card_id', $rateCardId)
            ->where('service_type',  $serviceType)
            ->where('zone_code',     $zone)
            ->where('weight_from',   '<=', $chargeableWeight)
            ->where('weight_to',     '>=', $chargeableWeight)
            ->first();

        if (! $matrix) return null;

        $baseRate  = (float) $matrix->base_rate;
        $fuelPct   = (float) $matrix->fuel_surcharge_pct;
        $fuelCharge = round($baseRate * $fuelPct / 100, 2);
        $oda        = $destPin[0] === '9' ? (float) $matrix->oda_flat : 0;
        $subtotal   = $baseRate + $fuelCharge + $oda;
        $gst        = round($subtotal * 0.18, 2);

        return [
            'zone'          => $zone,
            'base_freight'  => $baseRate,
            'fuel_surcharge' => $fuelCharge,
            'fuel_pct'      => $fuelPct,
            'oda_charge'    => $oda,
            'gst'           => $gst,
            'total'         => round($subtotal + $gst, 2),
        ];
    }

    private function getZoneFromPincodes(string $origin, string $dest): string
    {
        if (substr($origin, 0, 3) === substr($dest, 0, 3)) return 'A';
        $o1 = (int) $origin[0];
        $d1 = (int) $dest[0];
        $o3 = (int) substr($origin, 0, 3);
        $d3 = (int) substr($dest,   0, 3);
        if ($o1 === $d1 && abs($o3 - $d3) <= 20) return 'B';
        if ($o1 === $d1) return 'C';
        if ($o1 === 9 || $d1 === 9) return 'E';
        return 'D';
    }
}
