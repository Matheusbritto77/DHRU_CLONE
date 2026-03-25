<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DhruServiceChange extends Model
{
    protected $fillable = [
        'dhru_provider_id',
        'dhru_catalog_service_id',
        'change_type',
        'changed_fields',
        'snapshot_before',
        'snapshot_after',
        'detected_at',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'snapshot_before' => 'array',
        'snapshot_after' => 'array',
        'detected_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(DhruProvider::class, 'dhru_provider_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(DhruCatalogService::class, 'dhru_catalog_service_id');
    }
}
