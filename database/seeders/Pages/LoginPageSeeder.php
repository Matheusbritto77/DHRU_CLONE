<?php

namespace Database\Seeders\Pages;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Plugin;
use Illuminate\Database\Seeder;

class LoginPageSeeder extends Seeder
{
    /**
     * @var string[]
     */
    private array $pluginSlugs = [
        'auth-login-header',
        'auth-login-form',
        'auth-login-support',
        'dark-mode-toggle',
    ];

    public function run(): void
    {
        $page = Page::updateOrCreate(['slug' => 'login'], [
            'title' => 'Login',
            'route_name' => 'login',
            'is_system' => true,
            'is_active' => true,
            'meta' => [
                'title' => 'Entrar - BR Server',
                'description' => 'Acesse sua conta com rapidez e seguranca.',
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
