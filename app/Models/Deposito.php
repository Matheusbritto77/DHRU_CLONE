<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposito extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'depositos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'txid',
        'gateway_slug',
        'gateway_reference',
        'webhook_token',
        'gateway_payload',
        'webhook_last_payload',
        'valor',
        'user_id',
        'status',
        'payment_status_detail',
        'paid_at',
        'credited_at',
        'credited_amount',
    ];

    protected $casts = [
        'gateway_payload' => 'array',
        'webhook_last_payload' => 'array',
        'paid_at' => 'datetime',
        'credited_at' => 'datetime',
    ];
}
