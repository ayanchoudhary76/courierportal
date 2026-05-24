<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index(Request $request, string $awb = null)
    {
        $awb     = $awb ?? $request->query('awb');
        $booking = null;
        $events  = collect();

        if ($awb) {
            $booking = Booking::where('awb_number', strtoupper(trim($awb)))
                ->with(['trackingEvents' => fn ($q) => $q->orderBy('event_time')])
                ->first();

            $events = $booking ? $booking->trackingEvents : collect();
        }

        return view('client.tracking', compact('awb', 'booking', 'events'));
    }
}
