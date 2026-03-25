<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Model
{
    protected $fillable = [
        'page_id', 'plugin_id', 'sort_order', 'is_visible', 'settings',
    ];

    protected $attributes = [
        'settings' => '[]',
        'is_visible' => true,
    ];

    protected $casts = [
        'settings'   => 'array',
        'is_visible' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class);
    }

    /**
     * Renderiza o bloco usando o plugin, com as settings customizadas deste bloco.
     */
    public function render(array $context = []): string
    {
        if (!$this->plugin || !$this->plugin->is_active) {
            return '';
        }

        return $this->plugin->render($this->settings ?? [], $context);
    }
}
