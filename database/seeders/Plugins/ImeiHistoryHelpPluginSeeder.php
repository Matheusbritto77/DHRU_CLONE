<?php

namespace Database\Seeders\Plugins;

class ImeiHistoryHelpPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'imei-history-help';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Auxiliar Historico IMEI',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-information-circle',
            'description' => 'Bloco auxiliar do historico IMEI.',
            'default_settings' => [
                'title' => 'Como ler os status',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pb-12 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-[32px] p-7 md:p-8 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
            <div class="mt-6 grid gap-4 md:grid-cols-4">
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">Preparando</p><p class="mt-2 text-sm leading-7 theme-muted">Pedido recebido e aguardando processamento inicial.</p></div>
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">Processando</p><p class="mt-2 text-sm leading-7 theme-muted">Ordem em andamento na API ou em fila de retorno.</p></div>
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">Rejeitado</p><p class="mt-2 text-sm leading-7 theme-muted">A ordem nao foi concluida com sucesso.</p></div>
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">Sucesso</p><p class="mt-2 text-sm leading-7 theme-muted">Pedido finalizado com retorno valido.</p></div>
            </div>
        </div>
        <div class="rounded-[32px] p-7 md:p-8 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">Atalhos</p>
            <div class="mt-5 space-y-3">
                <a href="{{ route('imei.index') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Abrir servicos IMEI</a>
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
