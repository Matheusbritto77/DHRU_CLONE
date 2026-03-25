<?php

namespace Database\Seeders\Plugins;

class ImeiServicesHeroPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'imei-services-hero';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Hero IMEI Services',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-device-phone-mobile',
            'description' => 'Cabecalho da pagina de servicos IMEI.',
            'default_settings' => [
                'eyebrow' => 'IMEI Services',
                'title' => 'Escolha um servico com foco e rapidez.',
                'description' => 'Pesquise, filtre por grupo e envie pedidos IMEI em uma interface mais limpa e consistente com o restante do painel.',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pt-6 lg:pt-10 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="rounded-[36px] p-8 md:p-10 theme-panel-strong" style="background-image: radial-gradient(circle at top left, color-mix(in srgb, var(--theme-accent) 15%, transparent), transparent 36%);">
            <p class="text-[11px] uppercase tracking-[0.35em] theme-muted">{{ $settings['eyebrow'] }}</p>
            <div class="mt-4 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <h1 class="text-3xl md:text-5xl font-semibold tracking-tight theme-text">{{ $settings['title'] }}</h1>
                    <p class="mt-4 text-base md:text-lg leading-8 theme-muted">{{ $settings['description'] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:min-w-[280px]">
                    <div class="rounded-[24px] px-5 py-4 theme-soft">
                        <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Servicos</p>
                        <p class="mt-3 text-2xl font-semibold theme-text">{{ count($filteredServices ?? []) }}</p>
                    </div>
                    <div class="rounded-[24px] px-5 py-4 theme-soft">
                        <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Grupos</p>
                        <p class="mt-3 text-2xl font-semibold theme-text">{{ collect($filteredServices ?? [])->pluck('GROUPNAME')->unique()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
