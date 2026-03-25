<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'slug', 'title', 'route_name', 'is_system', 'is_active',
        'meta', 'middlewares', 'sort_order',
    ];

    protected $casts = [
        'meta'        => 'array',
        'middlewares' => 'array',
        'is_system'   => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('sort_order');
    }

    /**
     * Retorna todos os blocos visíveis da página com seus plugins, renderizados.
     */
    public function renderBlocks(): string
    {
        return $this->blocks()
            ->where('is_visible', true)
            ->with('plugin')
            ->get()
            ->map(fn (PageBlock $block) => $block->render())
            ->implode("\n");
    }
}
