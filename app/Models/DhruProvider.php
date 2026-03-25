<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DhruProvider extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'username',
        'api_key',
        'is_active',
        'sync_interval_minutes',
        'last_synced_at',
        'last_sync_status',
        'last_sync_message',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
        'meta' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (DhruProvider $provider): void {
            if (blank($provider->slug) && filled($provider->name)) {
                $provider->slug = Str::slug($provider->name);
            }
        });
    }

    public function services(): HasMany
    {
        return $this->hasMany(DhruCatalogService::class);
    }

    public function syncRuns(): HasMany
    {
        return $this->hasMany(DhruSyncRun::class);
    }

    public function serviceChanges(): HasMany
    {
        return $this->hasMany(DhruServiceChange::class);
    }
}
