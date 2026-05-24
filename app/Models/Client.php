<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'gstin',
        'address',
        'city',
        'pincode',
        'state',
        'account_type',
        'credit_limit',
        'rate_card_id',
        'created_by_admin',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'credit_limit' => 'decimal:2',
        ];
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rateCard()
    {
        return $this->belongsTo(RateCard::class);
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(User::class, 'created_by_admin');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }
}
