<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class ImeiServicesPageSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private array $pluginSlugs = [
        'dashboard-sidebar',
        'imei-services-hero',
        'imei-services-table',
        'imei-services-help',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $page = Page::updateOrCreate(['slug' => 'imei-services'], [
            'title' => 'IMEI Services',
            'route_name' => 'imei.index',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'IMEI Services - BR Server',
                'description' => 'Catalogo de servicos IMEI com busca, filtro e envio rapido de pedidos.',
            ],
        ]);

        $plugins = Plugin::query()
            ->whereIn('slug', $this->pluginSlugs)
            ->get()
            ->keyBy('slug');

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
