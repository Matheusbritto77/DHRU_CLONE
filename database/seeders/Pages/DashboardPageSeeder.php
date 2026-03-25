<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class DashboardPageSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private array $pluginSlugs = [
        'dashboard-sidebar',
        'dashboard-hero',
        'dashboard-stats',
        'dashboard-quick-links',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $page = Page::updateOrCreate(['slug' => 'dashboard'], [
            'title' => 'Dashboard',
            'route_name' => 'dashboard',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'Dashboard - BR Server',
                'description' => 'Painel principal com visao geral da conta.',
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
