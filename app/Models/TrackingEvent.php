<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    protected $fillable = [
        'booking_id',
        'event_type',
        'location',
        'remarks',
        'event_time',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_time' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
