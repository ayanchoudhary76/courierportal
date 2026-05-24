<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $request->validate(['awb_number' => ['nullable', 'string']]);

        $booking = null;
        if ($request->awb_number) {
            $booking = Booking::where('awb_number', strtoupper($request->awb_number))
                ->where('client_id', Auth::user()->client?->id)
                ->first();
        }

        return view('client.payment.initiate', compact('booking'));
    }

    public function verify(Request $request)
    {
        return redirect()->route('client.payment.success');
    }

    public function success()
    {
        return view('client.payment.success');
    }

    public function failed()
    {
        return view('client.payment.failed');
    }
}
