<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
    color: #000;
    width: 378px;
  }
  .label-wrap {
    width: 378px;
    min-height: 567px;
    border: 2px solid #000;
    padding: 0;
  }
  .header {
    background: #1e293b;
    color: #fff;
    padding: 10px 14px;
    border-bottom: 2px solid #000;
  }
  .header-inner {
    display: table;
    width: 100%;
  }
  .header-left {
    display: table-cell;
    vertical-align: middle;
  }
  .header-right {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
  }
  .brand {
    font-size: 16px;
    font-weight: bold;
    letter-spacing: 1px;
  }
  .brand-sub {
    font-size: 9px;
    color: #94a3b8;
    margin-top: 2px;
  }
  .header-phone {
    font-size: 10px;
    color: #cbd5e1;
  }
  .header-email {
    font-size: 9px;
    color: #64748b;
    margin-top: 2px;
  }
  .service-badge {
    text-align: center;
    background: #f1f5f9;
    border-bottom: 2px solid #000;
    padding: 6px;
    font-size: 13px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
  }
  .address-row {
    display: table;
    width: 100%;
    border-bottom: 1.5px solid #000;
  }
  .from-col {
    display: table-cell;
    width: 40%;
    padding: 10px 12px;
    vertical-align: top;
    border-right: 1.5px solid #000;
  }
  .to-col {
    display: table-cell;
    width: 60%;
    padding: 10px 12px;
    vertical-align: top;
  }
  .section-label {
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 1px;
    margin-bottom: 4px;
  }
  .from-name {
    font-size: 11px;
    font-weight: bold;
    margin-bottom: 3px;
  }
  .to-name {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 4px;
  }
  .address-text {
    font-size: 10px;
    line-height: 1.5;
    color: #1e293b;
  }
  .pincode-highlight {
    font-size: 18px;
    font-weight: 900;
    letter-spacing: 3px;
    margin-top: 6px;
    display: block;
    font-family: DejaVu Sans Mono, monospace;
  }
  .meta-row {
    display: table;
    width: 100%;
    border-bottom: 1.5px solid #000;
  }
  .meta-cell {
    display: table-cell;
    padding: 7px 8px;
    text-align: center;
    vertical-align: middle;
    border-right: 1px solid #ccc;
  }
  .meta-cell-last {
    display: table-cell;
    padding: 7px 8px;
    text-align: center;
    vertical-align: middle;
  }
  .meta-label {
    font-size: 8px;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.5px;
  }
  .meta-value {
    font-size: 12px;
    font-weight: bold;
    margin-top: 2px;
  }
  .awb-section {
    text-align: center;
    padding: 10px 14px 6px;
    border-bottom: 1.5px solid #000;
  }
  .awb-label-text {
    font-size: 9px;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 1px;
    margin-bottom: 4px;
  }
  .awb-number {
    font-family: DejaVu Sans Mono, monospace;
    font-size: 20px;
    font-weight: bold;
    letter-spacing: 3px;
  }
  .barcode-section {
    text-align: center;
    padding: 8px 14px;
    border-bottom: 1.5px solid #000;
  }
  .footer-row {
    display: table;
    width: 100%;
  }
  .footer-cell {
    display: table-cell;
    padding: 6px 10px;
    font-size: 9px;
    color: #475569;
    vertical-align: middle;
    border-right: 1px solid #e2e8f0;
  }
  .footer-cell-last {
    display: table-cell;
    padding: 6px 10px;
    font-size: 9px;
    color: #475569;
    vertical-align: middle;
  }
  .payment-badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 9px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .badge-account { background: #dbeafe; color: #1d4ed8; }
  .badge-online  { background: #dcfce7; color: #166534; }
</style>
</head>
<body>
<div class="label-wrap">

  {{-- HEADER --}}
  <div class="header">
    <div class="header-inner">
      <div class="header-left">
        <div class="brand">COURIERPORTAL</div>
        <div class="brand-sub">Logistics Solutions</div>
      </div>
      <div class="header-right">
        <div class="header-phone">+91-98765-43210</div>
        <div class="header-email">info@courierportal.com</div>
      </div>
    </div>
  </div>

  {{-- SERVICE BADGE --}}
  <div class="service-badge">
    {{ strtoupper(str_replace('_', ' ', $booking->service_type)) }}
  </div>

  {{-- FROM / TO --}}
  <div class="address-row">
    <div class="from-col">
      <div class="section-label">From</div>
      <div class="from-name">{{ $booking->sender_name }}</div>
      <div class="address-text">
        {{ $booking->sender_address }}<br>
        Pin: <strong>{{ $booking->sender_pincode }}</strong><br>
        Ph: {{ $booking->sender_phone }}
      </div>
    </div>
    <div class="to-col">
      <div class="section-label">Deliver To</div>
      <div class="to-name">{{ strtoupper($booking->receiver_name) }}</div>
      <div class="address-text">
        {{ $booking->receiver_address }}<br>
        Ph: {{ $booking->receiver_phone }}
      </div>
      <span class="pincode-highlight">{{ $booking->receiver_pincode }}</span>
    </div>
  </div>

  {{-- META ROW --}}
  <div class="meta-row">
    <div class="meta-cell">
      <div class="meta-label">Weight</div>
      <div class="meta-value">{{ number_format($booking->weight_actual, 3) }} kg</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Pieces</div>
      <div class="meta-value">{{ $booking->pieces }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Parcel Type</div>
      <div class="meta-value">{{ ucwords(str_replace('_', ' ', $booking->parcel_type)) }}</div>
    </div>
    <div class="meta-cell-last">
      <div class="meta-label">Declared Value</div>
      <div class="meta-value">Rs.{{ number_format($booking->declared_value, 2) }}</div>
    </div>
  </div>

  {{-- AWB NUMBER --}}
  <div class="awb-section">
    <div class="awb-label-text">AWB / Tracking Number</div>
    <div class="awb-number">{{ $booking->awb_number }}</div>
  </div>

  {{-- BARCODE --}}
  <div class="barcode-section">
    {!! DNS1D::getBarcodeHTML($booking->awb_number, 'C128', 1.5, 40, 'black', false) !!}
  </div>

  {{-- FOOTER --}}
  <div class="footer-row">
    <div class="footer-cell">
      <strong>Booking Date:</strong><br>
      {{ $booking->created_at->format('d M Y') }}
    </div>
    <div class="footer-cell">
      <strong>Amount:</strong><br>
      Rs.{{ number_format($booking->total_amount, 2) }}
    </div>
    <div class="footer-cell">
      <strong>Payment:</strong><br>
      @if($booking->payment_mode === 'bill_to_account')
        <span class="payment-badge badge-account">BILL A/C</span>
      @else
        <span class="payment-badge badge-online">ONLINE</span>
      @endif
    </div>
    <div class="footer-cell-last">
      <strong>Status:</strong><br>
      {{ ucwords(str_replace('_', ' ', $booking->booking_status)) }}
    </div>
  </div>

</div>
</body>
</html>
