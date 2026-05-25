@extends('client.layouts.app')
@section('page-title', 'Payment Successful')

@section('content')
<div style="max-width:520px; margin:60px auto; padding:0 20px; text-align:center;">
    <div style="background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(0,0,0,0.09);
                padding:52px 36px;">

        {{-- Animated check icon --}}
        <div style="width:80px; height:80px; background:#f0fdf4; border-radius:50%;
                    display:flex; align-items:center; justify-content:center;
                    margin:0 auto 20px; border:3px solid #bbf7d0;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                 stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>

        <h1 style="font-size:26px; font-weight:800; color:#15803d; margin:0 0 8px;">
            Payment Successful!
        </h1>
        <p style="color:#64748b; font-size:15px; margin:0 0 28px; line-height:1.6;">
            Your shipment has been confirmed and is being processed.
            You'll receive a confirmation email shortly.
        </p>

        @if(session('awb'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px;
                    padding:20px; margin-bottom:28px; text-align:left;">
            <div style="font-size:11px; font-weight:700; color:#16a34a; letter-spacing:0.08em;
                        text-transform:uppercase; margin-bottom:6px;">
                AWB Number
            </div>
            <div style="font-family:monospace; font-size:24px; font-weight:900; color:#15803d;
                        margin-bottom:12px; letter-spacing:0.05em;">
                {{ session('awb') }}
            </div>
            @if(session('payment_id'))
            <div style="display:flex; justify-content:space-between; font-size:12.5px;
                        color:#64748b; margin-bottom:4px;">
                <span>Payment ID</span>
                <span style="font-family:monospace; font-weight:600;">{{ session('payment_id') }}</span>
            </div>
            @endif
            @if(session('amount'))
            <div style="display:flex; justify-content:space-between; font-size:14px;
                        font-weight:700; color:#16a34a; margin-top:8px;
                        padding-top:8px; border-top:1px solid #dcfce7;">
                <span>Amount Paid</span>
                <span>&#8377;{{ number_format(session('amount'), 2) }}</span>
            </div>
            @endif
        </div>
        @endif

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            @if(session('awb'))
            <a href="{{ route('client.bookings.show', session('awb')) }}"
               style="padding:12px 26px; background:#2563eb; color:#fff; border-radius:9px;
                      text-decoration:none; font-weight:700; font-size:14px;
                      box-shadow:0 2px 8px rgba(37,99,235,0.3);">
                View Booking
            </a>
            @endif
            <a href="{{ route('client.bookings') }}"
               style="padding:12px 26px; background:#f1f5f9; color:#475569; border-radius:9px;
                      text-decoration:none; font-weight:700; font-size:14px;
                      border:1px solid #e2e8f0;">
                My Bookings
            </a>
        </div>
    </div>
</div>
@endsection
