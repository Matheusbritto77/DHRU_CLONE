<?php

namespace Database\Seeders\Plugins;

class DashboardHeroPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'dashboard-hero';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Hero do Dashboard',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-sparkles',
            'description' => 'Bloco de abertura do dashboard com saudacao e contexto.',
            'default_settings' => [
                'eyebrow' => 'Visao geral',
                'title_prefix' => 'Bem-vindo de volta',
                'description' => 'Acompanhe creditos, pedidos e o desempenho da sua conta em uma tela simples e direta.',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pt-6 lg:pt-10 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="rounded-[36px] p-8 md:p-10 theme-panel-strong" style="background-image: radial-gradient(circle at top left, color-mix(in srgb, var(--theme-accent) 16%, transparent), transparent 36%);">
            <p class="text-[11px] uppercase tracking-[0.35em] theme-muted">{{ $settings['eyebrow'] }}</p>
            <h1 class="mt-4 text-3xl md:text-5xl font-semibold tracking-tight theme-text">
                {{ $settings['title_prefix'] }}, {{ Str::of(Auth::user()->name)->explode(' ')->first() }}.
            </h1>
            <p class="mt-4 max-w-3xl text-base md:text-lg theme-muted leading-8">
                {{ $settings['description'] }}
            </p>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
