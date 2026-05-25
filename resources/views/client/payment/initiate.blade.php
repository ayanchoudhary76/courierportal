@extends('client.layouts.app')
@section('page-title', 'Complete Payment')

@push('styles')
<style>
.pay-wrap { max-width: 540px; margin: 48px auto; padding: 0 20px; }
.pay-card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.09);
    padding: 36px 32px;
    margin-bottom: 20px;
}
.pay-header { text-align: center; margin-bottom: 28px; }
.pay-icon { font-size: 40px; margin-bottom: 10px; }
.pay-title { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 6px; }
.pay-sub { color: #64748b; font-size: 13.5px; }

.detail-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
}
.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13.5px;
}
.detail-row:last-child { border-bottom: none; padding-bottom: 0; }
.detail-label { color: #64748b; }
.detail-val { font-weight: 600; color: #0f172a; }
.detail-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 2px solid #e2e8f0;
}
.detail-total-label { font-size: 15px; font-weight: 700; color: #0f172a; }
.detail-total-amount { font-size: 26px; font-weight: 900; color: #16a34a; }

.btn-pay {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: 0.3px;
    transition: opacity 0.15s, transform 0.1s;
    box-shadow: 0 4px 14px rgba(37,99,235,0.35);
}
.btn-pay:hover { opacity: 0.93; transform: translateY(-1px); }
.btn-pay:active { transform: translateY(0); }

.secure-note {
    text-align: center;
    margin-top: 12px;
    font-size: 12px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.cancel-link {
    display: block;
    text-align: center;
    color: #64748b;
    font-size: 13px;
    text-decoration: none;
    transition: color 0.15s;
}
.cancel-link:hover { color: #2563eb; }

.rzp-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
    padding: 10px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    font-size: 12px;
    color: #64748b;
}
</style>
@endpush

@section('content')
<div class="pay-wrap">

    {{-- Booking Summary + Pay Card --}}
    <div class="pay-card">
        <div class="pay-header">
            <div class="pay-icon">💳</div>
            <h1 class="pay-title">Complete Your Payment</h1>
            <p class="pay-sub">Review your booking and pay securely via Razorpay</p>
        </div>

        {{-- Booking breakdown --}}
        <div class="detail-box">
            <div class="detail-row">
                <span class="detail-label">AWB Number</span>
                <span class="detail-val" style="font-family:monospace; color:#2563eb;">
                    {{ $booking->awb_number }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Service Type</span>
                <span class="detail-val">{{ ucwords(str_replace('_', ' ', $booking->service_type)) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Destination Pincode</span>
                <span class="detail-val" style="font-family:monospace;">{{ $booking->receiver_pincode }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Weight</span>
                <span class="detail-val">{{ $booking->weight_actual }} kg</span>
            </div>

            <div class="detail-total">
                <span class="detail-total-label">Total Amount</span>
                <span class="detail-total-amount">&#8377;{{ number_format($booking->total_amount, 2) }}</span>
            </div>
        </div>

        {{-- Pay Button --}}
        <button id="rzp-button" class="btn-pay">
            Pay &#8377;{{ number_format($booking->total_amount, 2) }} Now
        </button>

        <div class="secure-note">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            256-bit SSL encrypted &bull; Secured by Razorpay
        </div>

        <div class="rzp-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Payments processed securely by Razorpay. Your card details are never shared with us.
        </div>
    </div>

    <a href="{{ route('client.bookings.show', $booking->awb_number) }}" class="cancel-link">
        ← Cancel and go back to booking
    </a>
</div>

{{-- Hidden form: POST to our verify route after Razorpay success --}}
<form id="payment-form" method="POST" action="{{ route('client.payment.verify') }}" style="display:none;">
    @csrf
    <input type="hidden" name="razorpay_order_id"   id="razorpay_order_id">
    <input type="hidden" name="razorpay_payment_id"  id="razorpay_payment_id">
    <input type="hidden" name="razorpay_signature"   id="razorpay_signature">
</form>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        key:         "{{ $razorpayKey }}",
        amount:      {{ $amountInPaise }},
        currency:    "INR",
        name:        "CourierPortal",
        description: "Payment for AWB: {{ $booking->awb_number }}",
        image:       "",
        order_id:    "{{ $razorpayOrderId }}",
        prefill: {
            name:    "{{ addslashes($userName) }}",
            email:   "{{ $userEmail }}",
            contact: "{{ $userPhone }}"
        },
        theme: {
            color: "#2563eb"
        },
        handler: function (response) {
            // Called on successful payment — POST to our verify endpoint
            document.getElementById('razorpay_order_id').value   = response.razorpay_order_id;
            document.getElementById('razorpay_payment_id').value  = response.razorpay_payment_id;
            document.getElementById('razorpay_signature').value   = response.razorpay_signature;
            document.getElementById('payment-form').submit();
        },
        modal: {
            ondismiss: function () {
                console.log('Payment modal closed by user.');
            }
        }
    };

    var rzp = new Razorpay(options);

    document.getElementById('rzp-button').addEventListener('click', function (e) {
        e.preventDefault();
        rzp.open();
    });
</script>
@endpush
