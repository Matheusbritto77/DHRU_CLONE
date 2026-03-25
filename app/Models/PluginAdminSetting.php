<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PluginAdminSetting extends Model
{
    protected $fillable = [
        'plugin_id',
        'settings',
    ];

    protected $attributes = [
        'settings' => '[]',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }
}
