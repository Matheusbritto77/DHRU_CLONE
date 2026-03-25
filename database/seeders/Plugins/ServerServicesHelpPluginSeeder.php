<?php

namespace Database\Seeders\Plugins;

class ServerServicesHelpPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'server-services-help';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Auxiliar Server Services',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-information-circle',
            'description' => 'Bloco auxiliar com orientacoes rapidas para servicos server.',
            'default_settings' => [
                'title' => 'Como evitar erros no pedido server',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pb-12 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
            <div class="rounded-[32px] p-7 md:p-8 theme-panel">
                <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Campos dinâmicos e quantidade, sem confusão.</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">1. Abra o servico</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">Clique na linha ou no card para abrir o modal com os campos exigidos para aquele servico.</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">2. Revise a quantidade</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">Quando houver `MINQNT` e `MAXQNT`, o custo total e recalculado automaticamente conforme a quantidade.</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">3. Acompanhe o retorno</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">Depois do envio, acompanhe o processamento em Historico Server para validar andamento e resultado.</p>
                    </div>
                </div>
            </div>
            <div class="rounded-[32px] p-7 md:p-8 theme-panel">
                <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">Atalhos</p>
                <div class="mt-5 space-y-3">
                    <a href="{{ route('Server.history') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Ver historico Server</a>
                    <a href="{{ route('add-credits') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Adicionar creditos</a>
                    <a href="{{ route('dashboard') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Voltar ao dashboard</a>
                </div>
            </div>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
