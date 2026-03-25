<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class RegisterPageSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private array $pluginSlugs = [
        'auth-register-header',
        'auth-register-form',
        'auth-register-support',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $page = Page::updateOrCreate(['slug' => 'register'], [
            'title' => 'Cadastro',
            'route_name' => 'register',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'Criar conta - BR Server',
                'description' => 'Crie sua conta e acesse a plataforma com uma experiencia simples e elegante.',
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
