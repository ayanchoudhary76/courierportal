<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateMatrix extends Model
{
    protected $table = 'rate_matrix';

    protected $fillable = [
        'rate_card_id',
        'service_type',
        'weight_from',
        'weight_to',
        'zone_code',
        'base_rate',
        'fuel_surcharge_pct',
        'oda_flat',
        'cod_pct',
    ];

    protected function casts(): array
    {
        return [
            'weight_from'        => 'decimal:3',
            'weight_to'          => 'decimal:3',
            'base_rate'          => 'decimal:2',
            'fuel_surcharge_pct' => 'decimal:2',
            'oda_flat'           => 'decimal:2',
            'cod_pct'            => 'decimal:2',
        ];
    }

    public function rateCard()
    {
        return $this->belongsTo(RateCard::class);
    }
}
