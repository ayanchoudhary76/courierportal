<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternationalRate extends Model
{
    protected $fillable = [
        'rate_card_id',
        'country_group',
        'service_type',
        'weight_from',
        'weight_to',
        'base_rate',
        'fuel_surcharge_pct',
    ];

    protected function casts(): array
    {
        return [
            'weight_from'        => 'decimal:3',
            'weight_to'          => 'decimal:3',
            'base_rate'          => 'decimal:2',
            'fuel_surcharge_pct' => 'decimal:2',
        ];
    }

    public function rateCard()
    {
        return $this->belongsTo(RateCard::class);
    }
}
