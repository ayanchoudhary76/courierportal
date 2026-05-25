<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    protected Api $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    // ── Show payment page + create Razorpay order ────────────────────────
    public function initiate(Request $request)
    {
        $request->validate([
            'awb_number' => 'required|string',
        ]);

        $client  = Auth::user()->client;
        $booking = Booking::where('awb_number', strtoupper($request->awb_number))
            ->where('client_id', $client->id)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        // Amount in paise (Razorpay uses smallest currency unit)
        $amountInPaise = (int) round($booking->total_amount * 100);

        // Create Razorpay order
        $razorpayOrder = $this->razorpay->order->create([
            'amount'   => $amountInPaise,
            'currency' => 'INR',
            'receipt'  => 'rcpt_' . $booking->awb_number,
            'notes'    => [
                'awb_number'  => $booking->awb_number,
                'client_id'   => $client->id,
                'client_name' => Auth::user()->name,
            ],
        ]);

        // Save / update a pending payment record
        Payment::updateOrCreate(
            ['booking_id' => $booking->id, 'status' => 'pending'],
            [
                'client_id'         => $client->id,
                'razorpay_order_id' => $razorpayOrder->id,
                'amount'            => $booking->total_amount,
                'currency'          => 'INR',
                'method'            => null,
                'status'            => 'pending',
            ]
        );

        return view('client.payment.initiate', [
            'booking'         => $booking,
            'razorpayOrderId' => $razorpayOrder->id,
            'razorpayKey'     => config('services.razorpay.key'),
            'amountInPaise'   => $amountInPaise,
            'userName'        => Auth::user()->name,
            'userEmail'       => Auth::user()->email,
            'userPhone'       => Auth::user()->phone ?? '9000000000',
        ]);
    }

    // ── Verify payment signature after Razorpay checkout ────────────────
    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        try {
            $this->razorpay->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('client.payment.failed')
                ->with('error', 'Payment verification failed. Please contact support.');
        }

        // Fetch payment method details from Razorpay
        try {
            $paymentDetails = $this->razorpay->payment->fetch($request->razorpay_payment_id);
            $method = $paymentDetails->method ?? 'razorpay';
        } catch (\Exception $e) {
            $method = 'razorpay';
        }

        // Find our payment record by order ID
        $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
        $booking = Booking::findOrFail($payment->booking_id);

        // Update payment record to success
        $payment->update([
            'gateway_txn_id' => $request->razorpay_payment_id,
            'method'         => $method,
            'status'         => 'success',
        ]);

        // Mark booking as paid
        $booking->update(['payment_status' => 'paid']);

        return redirect()->route('client.payment.success')
            ->with('success', 'Payment successful!')
            ->with('awb', $booking->awb_number)
            ->with('payment_id', $request->razorpay_payment_id)
            ->with('amount', $booking->total_amount);
    }

    // ── Success page ──────────────────────────────────────────────────────
    public function success()
    {
        if (! session('awb')) {
            return redirect()->route('client.bookings');
        }

        return view('client.payment.success');
    }

    // ── Failed page ───────────────────────────────────────────────────────
    public function failed()
    {
        return view('client.payment.failed');
    }
}
