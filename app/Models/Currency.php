<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_base',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_base' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * @param  float|decimal  $amount  The amount in the base currency (e.g. USD)
     * @return float|decimal  The converted amount in the user's currency
     */
    public function convertFromBase($amount): float
    {
        return (float) $amount * (float) $this->exchange_rate;
    }
}
