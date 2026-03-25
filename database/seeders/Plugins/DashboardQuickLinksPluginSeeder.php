<?php

namespace Database\Seeders\Plugins;

class DashboardQuickLinksPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'dashboard-quick-links';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Atalhos do Dashboard',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-bolt',
            'description' => 'Acoes rapidas e atalhos do dashboard.',
            'default_settings' => [
                'title' => 'Acoes rapidas',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pb-12 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="rounded-[32px] p-7 md:p-8 theme-panel">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Tudo o que voce precisa, sem ruido.</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('imei.index') }}" class="rounded-full px-5 py-3 text-sm font-medium shadow-sm theme-accent-bg">Novo pedido IMEI</a>
                    <a href="{{ route('server-services') }}" class="rounded-full px-5 py-3 text-sm font-medium" style="background: var(--theme-text); color: var(--theme-bg);">Novo pedido Server</a>
                    <a href="{{ route('add-credits') }}" class="rounded-full px-5 py-3 text-sm font-medium theme-soft theme-text">Adicionar creditos</a>
                </div>
            </div>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
