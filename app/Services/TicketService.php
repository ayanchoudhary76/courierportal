<?php

namespace App\Services;

use App\Models\SupportTicket;

class TicketService
{
    public static function generateNumber(): string
    {
        do {
            $num = 'TKT-' . date('Ym') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (SupportTicket::where('ticket_number', $num)->exists());

        return $num;
    }
}
