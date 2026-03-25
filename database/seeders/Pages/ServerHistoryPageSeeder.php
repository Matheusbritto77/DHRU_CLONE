<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class ServerHistoryPageSeeder extends Seeder
{
    private array $pluginSlugs = [
        'dashboard-sidebar',
        'server-history-hero',
        'server-history-table',
        'server-history-help',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $page = Page::updateOrCreate(['slug' => 'server-history'], [
            'title' => 'Historico Server',
            'route_name' => 'Server.history',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'Historico Server - BR Server',
                'description' => 'Acompanhe o historico de pedidos server.',
            ],
        ]);

        $plugins = Plugin::query()->whereIn('slug', $this->pluginSlugs)->get()->keyBy('slug');

        $page->blocks()->delete();

        $order = 0;
        foreach ($this->pluginSlugs as $slug) {
            $plugin = $plugins->get($slug);
            if (! $plugin) {
                continue;
            }

            PageBlock::create([
                'page_id' => $page->id,
                'plugin_id' => $plugin->id,
                'sort_order' => $slug === 'dark-mode-toggle' ? 99 : $order++,
                'is_visible' => true,
            ]);
        }
    }
}
