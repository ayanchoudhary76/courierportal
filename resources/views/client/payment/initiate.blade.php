@extends('client.layouts.app')
@section('page-title', 'Payment')

@section('content')
<div style="max-width:560px;margin:40px auto;padding:0 24px">
    <div style="background:#fff;border-radius:14px;border:1px solid #e2e8f0;padding:32px;text-align:center">
        <div style="font-size:48px;margin-bottom:14px">💳</div>
        <h1 style="font-size:20px;font-weight:800;color:#0f172a;margin-bottom:8px">Online Payment</h1>

        @if($booking)
        <div style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;padding:16px;margin:18px 0;text-align:left">
            <div style="font-size:11px;font-weight:700;color:#94a3b8;margin-bottom:8px;text-transform:uppercase">Booking Details</div>
            <div style="display:flex;justify-content:space-between;margin-bottom:6px;font-size:13.5px">
                <span style="color:#64748b">AWB</span><span style="font-weight:700;font-family:monospace">{{ $booking->awb_number }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:15px;font-weight:800;color:#1e40af;border-top:1px solid #e2e8f0;padding-top:8px;margin-top:6px">
                <span>Amount</span><span>₹{{ number_format($booking->total_amount,2) }}</span>
            </div>
        </div>
        @endif

        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:9px;padding:14px 16px;text-align:left;margin-bottom:20px">
            <div style="font-size:13px;font-weight:700;color:#b45309;margin-bottom:4px">⚙️ Razorpay Integration Pending</div>
            <div style="font-size:12.5px;color:#92400e;line-height:1.5">Configure <code>RAZORPAY_KEY</code> and <code>RAZORPAY_SECRET</code> in your <code>.env</code> file to activate live payments.</div>
        </div>

        <button onclick="alert('Configure Razorpay credentials in .env to activate.')"
                style="width:100%;padding:13px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer">
            🔐 Pay Now (Stub)
        </button>

        <a href="{{ route('client.bookings') }}" style="display:block;margin-top:14px;font-size:13px;color:#64748b;text-decoration:none">← Back to Bookings</a>
    </div>
</div>
@endsection
