<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Plugin;
use App\Models\Page;
use App\Models\PageBlock;

class InstallDarkMode extends Command
{
    protected $signature = 'plugin:install-darkmode';
    protected $description = 'Instala o plugin de Dark Mode e vincula à Welcome';

    public function handle(): int
    {
        $plugin = Plugin::updateOrCreate(['slug' => 'dark-mode-toggle'], [
            'name'  => 'Seletor de Tema (Claro/Escuro)',
            'type'  => 'custom',
            'is_system' => true,
            'is_active' => true,
            'icon' => 'heroicon-o-moon',
            'description' => 'Botão flutuante para alternar entre modo claro e escuro.',
            'default_settings' => ['size' => '48'],
            'blade_template' => '<div style="position:fixed;bottom:24px;right:24px;z-index:9999;">
    <button id="theme-toggle-btn" aria-label="Alternar tema" style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:1px solid #e5e5e5;background:#fff;box-shadow:0 8px 30px rgba(0,0,0,0.12);transition:all 0.3s ease;outline:none;">
        <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#1d1d1f" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 8.002-4.248Z"/></svg>
        <svg id="icon-sun" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#f5f5f7" stroke-width="1.5" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/></svg>
    </button>
</div>',
            'js' => '(function(){var html=document.documentElement;var btn=document.getElementById("theme-toggle-btn");var moon=document.getElementById("icon-moon");var sun=document.getElementById("icon-sun");if(!btn||!moon||!sun)return;function apply(dark){if(dark){html.classList.add("dark");moon.style.display="none";sun.style.display="block";btn.style.background="#2c2c2e";btn.style.borderColor="#555";}else{html.classList.remove("dark");moon.style.display="block";sun.style.display="none";btn.style.background="#fff";btn.style.borderColor="#e5e5e5";}}var stored=localStorage.getItem("theme");var prefersDark=window.matchMedia("(prefers-color-scheme:dark)").matches;apply(stored==="dark"||(stored===null&&prefersDark));btn.addEventListener("click",function(){var isDark=html.classList.contains("dark");localStorage.setItem("theme",isDark?"light":"dark");apply(!isDark);});})();',
            'css' => null,
            'routes' => null,
            'middlewares' => null,
        ]);

        $this->info("Plugin criado: ID {$plugin->id} | slug: {$plugin->slug}");

        $page = Page::where('slug', 'welcome')->first();

        if ($page) {
            PageBlock::updateOrCreate(
                ['page_id' => $page->id, 'plugin_id' => $plugin->id],
                ['sort_order' => 99, 'is_visible' => true]
            );
            $this->info("Vinculado à página Welcome (ID {$page->id})");
        } else {
            $this->warn("Página Welcome não encontrada!");
        }

        return self::SUCCESS;
    }
}
