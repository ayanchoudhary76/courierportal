<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateCard extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ──────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function rateMatrix()
    {
        return $this->hasMany(RateMatrix::class);
    }

    public function internationalRates()
    {
        return $this->hasMany(InternationalRate::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
