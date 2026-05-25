<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'client_id',
        'booking_id',
        'invoice_id',
        'gateway_txn_id',
        'razorpay_order_id',
        'amount',
        'currency',
        'method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
