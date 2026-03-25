<?php

namespace Database\Seeders\Plugins;

class HeroTextPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'hero-text';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Texto Principal (Hero)',
            'type' => 'hero',
            'is_system' => true,
            'icon' => 'heroicon-o-megaphone',
            'description' => 'Título chamativo, subtítulo e botões de ação.',
            'default_settings' => [
                'title' => 'Serviço de alta precisão.',
                'subtitle' => 'Integração contínua, potência avançada e o suporte que você merece. Tudo em um só lugar de maneira simples.',
                'cta_primary_text' => 'Comece Agora',
                'cta_secondary_text' => 'Fazer Login',
                'title_font_size' => '5xl',
                'title_color' => '#1d1d1f',
                'subtitle_color' => '#6e6e73',
                'btn_primary_color' => '#0071e3',
                'btn_secondary_color' => 'rgba(0,0,0,0.05)',
            ],
            'blade_template' => '<section class="pt-20 pb-16 px-4 text-center">
    <h1 style="color: {{ $settings[\'title_color\'] }};" class="text-{{ $settings[\'title_font_size\'] }} md:text-7xl font-semibold tracking-tighter mb-4 fade-in-up">{{ $settings[\'title\'] }}</h1>
    <h2 style="color: {{ $settings[\'subtitle_color\'] }};" class="text-xl md:text-2xl font-normal tracking-tight mb-10 max-w-3xl mx-auto fade-in-up">{{ $settings[\'subtitle\'] }}</h2>
    <div class="flex flex-col sm:flex-row justify-center items-center gap-4 fade-in-up">
        @if(Route::has(\'register\'))
            <a href="{{ route(\'register\') }}" style="background-color: {{ $settings[\'btn_primary_color\'] }};" class="text-white rounded-full px-6 py-3 text-[17px] font-medium hover:opacity-90 transition">{{ $settings[\'cta_primary_text\'] }}</a>
        @endif
        <a href="{{ route(\'login\') }}" style="background-color: {{ $settings[\'btn_secondary_color\'] }};" class="text-[#1d1d1f] rounded-full px-6 py-3 text-[17px] font-medium hover:opacity-80 transition">{{ $settings[\'cta_secondary_text\'] }}</a>
    </div>
</section>',
        ];
    }
}
