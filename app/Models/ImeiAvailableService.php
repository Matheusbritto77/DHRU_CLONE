<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImeiAvailableService extends Model
{
    protected $fillable = [
        'category_id',
        'dhru_catalog_service_id',
        'custom_name',
        'custom_description',
        'selling_price',
        'sort_order',
        'is_active',
        'service_type',
    ];

    protected $casts = [
        'selling_price' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ImeiServiceCategory::class, 'category_id');
    }

    public function catalogService(): BelongsTo
    {
        return $this->belongsTo(DhruCatalogService::class, 'dhru_catalog_service_id');
    }

    /**
     * Get the display name (custom if set, otherwise from catalog)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->custom_name ?: $this->catalogService->service_name;
    }

    /**
     * Get the current status of the upstream service
     */
    public function getUpstreamStatusAttribute(): bool
    {
        return (bool) $this->catalogService?->is_active;
    }
}
