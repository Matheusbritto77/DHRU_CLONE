<?php

namespace Database\Seeders\Plugins;

class ServerHistoryHelpPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'server-history-help';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Auxiliar Historico Server',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-information-circle',
            'description' => 'Bloco auxiliar do historico server.',
            'default_settings' => [
                'title' => 'Leitura rapida dos pedidos server',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pb-12 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-[32px] p-7 md:p-8 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">Quantidade</p><p class="mt-2 text-sm leading-7 theme-muted">Veja quantas unidades foram enviadas naquele pedido server.</p></div>
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">Referencia</p><p class="mt-2 text-sm leading-7 theme-muted">Use o `referenceid` para correlacionar o pedido com o retorno da API.</p></div>
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">Codigo</p><p class="mt-2 text-sm leading-7 theme-muted">O codigo mostra o resultado final ou o valor retornado pelo processamento.</p></div>
            </div>
        </div>
        <div class="rounded-[32px] p-7 md:p-8 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">Atalhos</p>
            <div class="mt-5 space-y-3">
                <a href="{{ route('server-services') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Abrir servicos Server</a>
                <a href="{{ route('add-credits') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Adicionar creditos</a>
                <a href="{{ route('dashboard') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Voltar ao dashboard</a>
            </div>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
