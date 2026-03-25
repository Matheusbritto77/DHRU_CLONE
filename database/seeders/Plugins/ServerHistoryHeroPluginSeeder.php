<?php

namespace Database\Seeders\Plugins;

class ServerHistoryHeroPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'server-history-hero';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Hero Historico Server',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-clock',
            'description' => 'Cabecalho do historico server.',
            'default_settings' => [
                'eyebrow' => 'Historico Server',
                'title' => 'Todos os pedidos server em um unico fluxo.',
                'description' => 'Consulte quantidade, custo, referencia e codigo de retorno com o mesmo padrao visual do restante do painel.',
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
                        <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Registros</p>
                        <p class="mt-3 text-2xl font-semibold theme-text">{{ $serverOrders->total() ?? 0 }}</p>
                    </div>
                    <div class="rounded-[24px] px-5 py-4 theme-soft">
                        <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Pagina</p>
                        <p class="mt-3 text-2xl font-semibold theme-text">{{ $serverOrders->currentPage() ?? 1 }}</p>
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
