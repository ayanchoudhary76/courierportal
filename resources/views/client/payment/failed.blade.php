@extends('client.layouts.app')
@section('page-title', 'Payment Failed')

@section('content')
<div style="max-width:520px; margin:60px auto; padding:0 20px; text-align:center;">
    <div style="background:#fff; border-radius:18px; box-shadow:0 4px 24px rgba(0,0,0,0.09);
                padding:52px 36px;">

        {{-- X icon --}}
        <div style="width:80px; height:80px; background:#fef2f2; border-radius:50%;
                    display:flex; align-items:center; justify-content:center;
                    margin:0 auto 20px; border:3px solid #fecaca;">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                 stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </div>

        <h1 style="font-size:26px; font-weight:800; color:#dc2626; margin:0 0 8px;">
            Payment Failed
        </h1>
        <p style="color:#64748b; font-size:15px; margin:0 0 8px; line-height:1.6;">
            {{ session('error') ?? 'Your payment could not be processed.' }}
        </p>
        <p style="color:#94a3b8; font-size:13px; margin:0 0 32px;">
            No amount has been deducted from your account. Please try again.
        </p>

        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px;
                    padding:14px 18px; margin-bottom:28px; text-align:left;
                    font-size:13px; color:#7f1d1d; line-height:1.6;">
            <strong>Common reasons for failure:</strong><br>
            Insufficient balance &bull; Incorrect card details &bull; Bank declined &bull; Session timeout
        </div>

        <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
            <a href="{{ route('client.bookings') }}"
               style="padding:12px 26px; background:#dc2626; color:#fff; border-radius:9px;
                      text-decoration:none; font-weight:700; font-size:14px;
                      box-shadow:0 2px 8px rgba(220,38,38,0.3);">
                Try Again
            </a>
            <a href="{{ route('client.dashboard') }}"
               style="padding:12px 26px; background:#f1f5f9; color:#475569; border-radius:9px;
                      text-decoration:none; font-weight:700; font-size:14px;
                      border:1px solid #e2e8f0;">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
