<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class AddCreditsPageSeeder extends Seeder
{
    private array $pluginSlugs = [
        'dashboard-sidebar',
        'add-credits-hero',
        'add-credits-form',
        'add-credits-help',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $page = Page::updateOrCreate(['slug' => 'add-credits'], [
            'title' => 'Adicionar Creditos',
            'route_name' => 'add-credits',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'Adicionar Creditos - BR Server',
                'description' => 'Pagina de recarga via PIX.',
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
