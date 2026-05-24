<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'awb_number',
        'client_id',
        'sender_name',
        'sender_address',
        'sender_pincode',
        'sender_phone',
        'receiver_name',
        'receiver_address',
        'receiver_pincode',
        'receiver_phone',
        'service_type',
        'parcel_type',
        'weight_actual',
        'weight_volumetric',
        'declared_value',
        'pieces',
        'base_amount',
        'surcharges',
        'total_amount',
        'payment_mode',
        'payment_status',
        'booking_status',
        'special_instructions',
    ];

    protected function casts(): array
    {
        return [
            'surcharges'        => 'array',
            'weight_actual'     => 'decimal:3',
            'weight_volumetric' => 'decimal:3',
            'declared_value'    => 'decimal:2',
            'base_amount'       => 'decimal:2',
            'total_amount'      => 'decimal:2',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function trackingEvents()
    {
        return $this->hasMany(TrackingEvent::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
