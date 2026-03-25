<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class ImeiHistoryPageSeeder extends Seeder
{
    private array $pluginSlugs = [
        'dashboard-sidebar',
        'imei-history-hero',
        'imei-history-table',
        'imei-history-help',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $page = Page::updateOrCreate(['slug' => 'imei-history'], [
            'title' => 'Historico IMEI',
            'route_name' => 'imei.history',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'Historico IMEI - BR Server',
                'description' => 'Acompanhe o historico de pedidos IMEI.',
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
