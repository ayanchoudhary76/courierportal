<?php

namespace App\Services;

use App\Models\Booking;

class AwbService
{
    public static function generate(): string
    {
        do {
            // Format: CP + year(2) + 8 random digits  e.g. CP2505001234
            $awb = 'CP' . date('y') . str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Booking::where('awb_number', $awb)->exists());

        return $awb;
    }
}
