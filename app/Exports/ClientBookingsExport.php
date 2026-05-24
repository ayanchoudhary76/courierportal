<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientBookingsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(
        private readonly int $clientId,
        private readonly string $from,
        private readonly string $to
    ) {}

    public function query()
    {
        return Booking::where('client_id', $this->clientId)
            ->whereBetween('created_at', [$this->from . ' 00:00:00', $this->to . ' 23:59:59'])
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'AWB Number', 'Date', 'Receiver Name', 'Destination Pincode',
            'Service Type', 'Weight (kg)', 'Status', 'Payment Status', 'Amount (₹)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->awb_number,
            $row->created_at->format('d M Y'),
            $row->receiver_name,
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
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
