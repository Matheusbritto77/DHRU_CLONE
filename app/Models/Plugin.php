<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plugin extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'description', 'version', 'author', 'is_system', 'is_active',
        'blade_template', 'css', 'js', 'default_settings', 'has_admin_panel', 'admin_panel_location',
        'admin_navigation_group', 'admin_navigation_label', 'admin_navigation_icon',
        'admin_page_title', 'admin_component_class', 'admin_form_schema',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'is_system'           => 'boolean',
        'has_admin_panel'     => 'boolean',
        'default_settings'    => 'array',
        'admin_form_schema'   => 'array',
    ];

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class);
    }

    public function adminSettings(): HasOne
    {
        return $this->hasOne(PluginAdminSetting::class);
    }

    public function getAdminNavigationLabel(): string
    {
        return $this->admin_navigation_label ?: $this->name;
    }

    public function render(array $settings = [], array $context = []): string
    {
        if (!$this->is_active) {
            return '';
        }
        
        try {
            $data = array_merge([
                'settings' => array_merge($this->default_settings ?? [], $settings),
                'plugin'   => $this,
                'context'  => $context,
            ], $context);

            return \Illuminate\Support\Facades\Blade::render($this->blade_template, $data);
        } catch (\Exception $e) {
            return "Error rendering plugin {$this->name}: " . $e->getMessage();
        }
    }
}
