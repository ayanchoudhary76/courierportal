@extends('client.layouts.app')
@section('page-title', 'Payment Failed')
@section('content')
<div style="max-width:480px;margin:60px auto;padding:0 24px;text-align:center">
    <div style="font-size:60px;margin-bottom:16px">❌</div>
    <h1 style="font-size:24px;font-weight:800;color:#dc2626;margin-bottom:8px">Payment Failed</h1>
    <p style="font-size:14px;color:#64748b;margin-bottom:24px">Something went wrong with your payment. Please try again or contact support.</p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
        <a href="{{ route('client.bookings') }}" style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;background:#2563eb;color:#fff;border-radius:9px;font-weight:600;text-decoration:none">← My Bookings</a>
        <a href="{{ route('client.tickets.create') }}" style="display:inline-flex;align-items:center;gap:8px;padding:11px 22px;border:1.5px solid #e2e8f0;color:#475569;border-radius:9px;font-weight:600;text-decoration:none">🎫 Contact Support</a>
    </div>
</div>
@endsection
