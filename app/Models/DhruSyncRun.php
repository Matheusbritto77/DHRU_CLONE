<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DhruSyncRun extends Model
{
    protected $fillable = [
        'dhru_provider_id',
        'status',
        'services_seen',
        'services_created',
        'services_updated',
        'services_removed',
        'message',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(DhruProvider::class, 'dhru_provider_id');
    }
}
