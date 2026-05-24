@extends('client.layouts.app')
@section('page-title', 'Payment Successful')
@section('content')
<div style="max-width:480px;margin:60px auto;padding:0 24px;text-align:center">
    <div style="font-size:60px;margin-bottom:16px">✅</div>
    <h1 style="font-size:24px;font-weight:800;color:#15803d;margin-bottom:8px">Payment Successful!</h1>
    <p style="font-size:14px;color:#64748b;margin-bottom:24px">Your payment has been processed successfully. Your booking is confirmed.</p>
    <a href="{{ route('client.bookings') }}" style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#2563eb;color:#fff;border-radius:9px;font-weight:600;text-decoration:none">← View My Bookings</a>
</div>
@endsection
