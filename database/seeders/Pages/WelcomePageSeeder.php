<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class WelcomePageSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private array $pluginSlugs = [
        'topbar',
        'navbar',
        'carousel-banners',
        'hero-text',
        'features-grid',
        'footer',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $welcomePage = Page::updateOrCreate(['slug' => 'welcome'], [
            'title' => 'Página Inicial',
            'route_name' => 'home',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'BR Server - Premium',
                'description' => 'Serviço de alta precisão com suporte 24/7.',
            ],
        ]);

        $plugins = Plugin::query()
            ->whereIn('slug', $this->pluginSlugs)
            ->get()
            ->keyBy('slug');

        $welcomePage->blocks()->delete();

        $order = 0;

        foreach ($this->pluginSlugs as $slug) {
            $plugin = $plugins->get($slug);

            if (! $plugin) {
                continue;
            }

            PageBlock::create([
                'page_id' => $welcomePage->id,
                'plugin_id' => $plugin->id,
                'sort_order' => $slug === 'dark-mode-toggle' ? 99 : $order++,
                'is_visible' => true,
            ]);
        }
    }
}
