@php
$colors = [
    // Booking statuses
    'booked'             => 'background:#f8fafc;color:#475569;border:1px solid #e2e8f0',
    'pickup_scheduled'   => 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe',
    'picked_up'          => 'background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe',
    'in_transit'         => 'background:#faf5ff;color:#7e22ce;border:1px solid #ddd6fe',
    'out_for_delivery'   => 'background:#fff7ed;color:#c2410c;border:1px solid #fed7aa',
    'delivered'          => 'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0',
    'failed'             => 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca',
    'returned'           => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a',
    // Ticket statuses
    'open'               => 'background:#fef2f2;color:#dc2626;border:1px solid #fecaca',
    'inprogress'         => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a',
    'resolved'           => 'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0',
    'closed'             => 'background:#f8fafc;color:#64748b;border:1px solid #e2e8f0',
    // Payment statuses
    'pending'            => 'background:#fffbeb;color:#b45309;border:1px solid #fde68a',
    'paid'               => 'background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0',
    'partial'            => 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe',
];
$style = $colors[$status] ?? 'background:#f8fafc;color:#64748b;border:1px solid #e2e8f0';
$label = ucwords(str_replace('_', ' ', $status));
@endphp
<span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;{{ $style }}">
    {{ $label }}
</span>
