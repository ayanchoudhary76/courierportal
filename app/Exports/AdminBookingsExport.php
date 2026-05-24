<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminBookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private readonly string $from,
        private readonly string $to,
        private readonly ?string $status = null,
        private readonly ?string $serviceType = null,
        private readonly ?int $clientId = null
    ) {}

    public function query()
    {
        return Booking::with('client.user')
            ->whereBetween('created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])
            ->when($this->status,      fn ($q) => $q->where('booking_status', $this->status))
            ->when($this->serviceType, fn ($q) => $q->where('service_type', $this->serviceType))
            ->when($this->clientId,    fn ($q) => $q->where('client_id', $this->clientId))
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'AWB Number', 'Client Name', 'Company', 'Date', 'Origin', 'Destination',
            'Service Type', 'Weight (kg)', 'Status', 'Payment', 'Amount (₹)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->awb_number,
            $row->client?->user?->name ?? '—',
            $row->client?->company_name ?? '—',
            $row->created_at->format('d M Y'),
            $row->sender_pincode,
            $row->receiver_pincode,
            str_replace('_', ' ', ucwords($row->service_type)),
            $row->weight_actual,
            str_replace('_', ' ', ucwords($row->booking_status)),
            ucwords($row->payment_status),
            number_format($row->total_amount, 2),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
