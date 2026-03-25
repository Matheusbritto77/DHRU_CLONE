<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Plugin;
use App\Models\Page;
use App\Models\PageBlock;

class DarkModeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar ou Atualizar o Plugin de Toggle usando DB para evitar problemas com Eloquent Events/Observers
        DB::table('plugins')->updateOrInsert(
            ['slug' => 'dark-mode-toggle'],
            [
                'name' => 'Dark Mode Toggle',
                'description' => 'Botão minimalista Apple para alternar entre Dark e Light mode.',
                'type' => 'custom',
                'is_system' => true,
                'is_active' => true,
                'icon' => 'heroicon-o-moon',
                'blade_template' => <<<'BLADE'
<div class="fixed bottom-8 right-8 z-[100] fade-in">
    <button id="theme-toggle" class="w-12 h-12 rounded-full bg-white/80 dark:bg-[#1d1d1f]/80 backdrop-blur-md border border-black/5 dark:border-white/10 shadow-2xl flex items-center justify-center text-[#1d1d1f] dark:text-gray-200 hover:scale-110 active:scale-95 transition-all duration-300 group">
        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-lg group-hover:text-[#0066cc]"></i>
        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-lg group-hover:text-[#ff9500]"></i>
    </button>
</div>

<script>
    (function() {
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (!themeToggleBtn) return;

        function applyTheme(isDark) {
            if (isDark) {
                document.documentElement.classList.add('dark');
                themeToggleLightIcon?.classList.remove('hidden');
                themeToggleDarkIcon?.classList.add('hidden');
            } else {
                document.documentElement.classList.remove('dark');
                themeToggleDarkIcon?.classList.remove('hidden');
                themeToggleLightIcon?.classList.add('hidden');
            }
        }

        const storedTheme = localStorage.getItem('color-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(storedTheme === 'dark' || (!storedTheme && prefersDark));

        themeToggleBtn.addEventListener('click', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';
            localStorage.setItem('color-theme', newTheme);
            applyTheme(!isDark);
        });
    })();
</script>
BLADE,
                'admin_form_schema' => json_encode([
                    ['name' => 'position', 'type' => 'text', 'label' => 'Posição (CSS)', 'default' => 'bottom-8 right-8'],
                ]),
                'default_settings' => json_encode([
                    'position' => 'bottom-8 right-8',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Atualizar templates dos outros plugins para suportar Dark Mode
        Plugin::where('slug', 'navbar')->update([
            'blade_template' => <<<'BLADE'
<nav class="backdrop-blur-xl backdrop-saturate-150 sticky top-0 z-50 transition-all duration-300 border-b bg-white/70 dark:bg-[#1d1d1f]/70 border-black/5 dark:border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14 items-center">
            <a href="/" class="flex-shrink-0 flex items-center">
                <img src="{{ asset($settings['logo_src']) }}" class="h-8 w-auto rounded" alt="Logo">
            </a>
            <div class="flex items-center space-x-6">
                <a href="{{ route('login') }}" class="text-sm font-medium text-[#1d1d1f] dark:text-gray-200 hover:text-[#0066cc] dark:hover:text-[#4da3ff] transition" style="text-decoration:none;">{{ $settings['login_text'] }}</a>
                @if($settings['show_register'] && Route::has('register'))
                    <a href="{{ route('register') }}" class="text-sm font-medium text-[#1d1d1f] dark:text-gray-200 hover:text-[#0066cc] dark:hover:text-[#4da3ff] transition" style="text-decoration:none;">{{ $settings['register_text'] }}</a>
                @endif
            </div>
        </div>
    </div>
</nav>
BLADE,
        ]);

        Plugin::where('slug', 'hero-text')->update([
            'blade_template' => <<<'BLADE'
<section class="pt-20 pb-16 px-4 text-center bg-[#f5f5f7] dark:bg-[#161617]">
    <h1 class="text-5xl md:text-7xl font-semibold tracking-tighter mb-4 fade-in-up text-[#1d1d1f] dark:text-white">{{ $settings['title'] }}</h1>
    <h2 class="text-xl md:text-2xl font-normal tracking-tight mb-10 max-w-3xl mx-auto fade-in-up text-gray-500 dark:text-gray-400">{{ $settings['subtitle'] }}</h2>
    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 fade-in-up">
        @if(Route::has('register'))
            <a href="{{ route('register') }}" style="background-color: {{ $settings['btn_primary_color'] }};" class="text-white rounded-full px-6 py-3 text-[17px] font-medium hover:opacity-90 transition">{{ $settings['cta_primary_text'] }}</a>
        @endif
        <a href="{{ route('login') }}" class="bg-black/5 dark:bg-white/10 text-[#1d1d1f] dark:text-gray-200 rounded-full px-6 py-3 text-[17px] font-medium hover:opacity-80 transition">{{ $settings['cta_secondary_text'] }}</a>
    </div>
</section>
BLADE,
        ]);

        Plugin::where('slug', 'features-grid')->update([
            'blade_template' => <<<'BLADE'
<section class="py-24 px-4 bg-white dark:bg-[#1d1d1f]">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 fade-in-up">
            <h2 class="text-4xl font-semibold tracking-tight text-[#1d1d1f] dark:text-white">{{ $settings['section_title'] }}</h2>
            <p class="mt-4 text-xl text-gray-500 dark:text-gray-400 font-light">{{ $settings['section_subtitle'] }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($settings['cards'] as $card)
            <div class="bg-[#f5f5f7] dark:bg-[#2c2c2e] border border-gray-100 dark:border-gray-800 rounded-3xl p-10 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 fade-in-up">
                <div style="background-color: {{ $card['icon_bg'] }}; color: {{ $card['icon_color'] }};" class="w-12 h-12 rounded-full flex items-center justify-center text-xl mb-6 shadow-sm"><i class="{{ $card['icon'] }}"></i></div>
                <h3 class="text-2xl font-semibold text-[#1d1d1f] dark:text-white mb-3 tracking-tight">{{ $card['title'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 leading-relaxed text-[16px] font-light">{{ $card['text'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
BLADE,
        ]);

        Plugin::where('slug', 'footer')->update([
            'blade_template' => <<<'BLADE'
<footer class="bg-white dark:bg-[#1d1d1f] border-t border-gray-200 dark:border-gray-800 pt-16 pb-8 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mb-12">
        <div>
            <img src="{{ asset($settings['logo_src']) }}" class="h-8 w-auto mb-6 rounded" alt="Logo">
            <p class="text-gray-500 dark:text-gray-400 text-sm font-light max-w-xs">{{ $settings['description'] }}</p>
        </div>
        <div>
            <h4 class="font-semibold text-[#1d1d1f] dark:text-white mb-4 text-sm">Siga-nos</h4>
            <div class="flex space-x-3">
                <a href="{{ $settings['whatsapp_link'] }}" class="w-9 h-9 rounded-full bg-[#f5f5f7] dark:bg-[#2c2c2e] flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-[#0066cc] hover:text-white transition group"><i class="fab fa-whatsapp group-hover:scale-110 transition-transform"></i></a>
                <a href="#" class="w-9 h-9 rounded-full bg-[#f5f5f7] dark:bg-[#2c2c2e] flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-[#0066cc] hover:text-white transition group"><i class="fab fa-instagram group-hover:scale-110 transition-transform"></i></a>
                <a href="#" class="w-9 h-9 rounded-full bg-[#f5f5f7] dark:bg-[#2c2c2e] flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-[#0066cc] hover:text-white transition group"><i class="fab fa-telegram group-hover:scale-110 transition-transform"></i></a>
            </div>
        </div>
        <div>
            <h4 class="font-semibold text-[#1d1d1f] dark:text-white mb-4 text-sm">Contatos</h4>
            <ul class="space-y-2 text-gray-500 dark:text-gray-400 text-sm font-light">
                <li><a href="mailto:{{ $settings['email'] }}" class="hover:text-[#0066cc] transition">{{ $settings['email'] }}</a></li>
                <li><a href="{{ $settings['whatsapp_link'] }}" class="hover:text-[#0066cc] transition">{{ $settings['whatsapp'] }}</a></li>
            </ul>
        </div>
    </div>
    <div class="max-w-7xl mx-auto border-t border-gray-100 dark:border-gray-800 pt-8 flex justify-between items-center text-xs text-gray-400 font-light">
        <p>&copy; <?php echo date("Y"); ?> {{ $settings['copyright'] }}</p>
    </div>
</footer>
BLADE,
        ]);

        // 3. Vincular Dark Mode Toggle à página Welcome
        $darkPlugin = Plugin::where('slug', 'dark-mode-toggle')->first();
        $welcomePage = Page::where('slug', 'welcome')->first();

        if ($darkPlugin && $welcomePage) {
            // Garantir que o bloco exista, usando DB para evitar erros de settings
            DB::table('page_blocks')->updateOrInsert(
                ['page_id' => $welcomePage->id, 'plugin_id' => $darkPlugin->id],
                [
                    'sort_order' => 99, 
                    'is_visible' => true, 
                    'settings' => '[]',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Todos os plugins atualizados com Dark Mode + Toggle vinculado!');
    }
}
