<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DhruCatalogService extends Model
{
    protected $fillable = [
        'dhru_provider_id',
        'group_name',
        'group_type',
        'service_type',
        'external_service_id',
        'service_name',
        'time_text',
        'cost',
        'min_qnt',
        'max_qnt',
        'custom_name',
        'custom_fields',
        'raw_payload',
        'fingerprint',
        'is_active',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'cost' => 'decimal:4',
        'custom_fields' => 'array',
        'raw_payload' => 'array',
        'is_active' => 'boolean',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(DhruProvider::class, 'dhru_provider_id');
    }

    public function customServices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ImeiAvailableService::class, 'dhru_catalog_service_id');
    }
}
