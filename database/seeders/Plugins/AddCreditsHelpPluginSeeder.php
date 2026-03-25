<?php

namespace Database\Seeders\Plugins;

class AddCreditsHelpPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'add-credits-help';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Auxiliar Adicionar Creditos',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-information-circle',
            'description' => 'Bloco auxiliar com orientacoes para recarga.',
            'default_settings' => [
                'title' => 'Como funciona a recarga',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pb-12 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
        <div class="rounded-[32px] p-7 md:p-8 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">1. Informe o valor</p><p class="mt-2 text-sm leading-7 theme-muted">Digite quanto deseja adicionar em USD para calcular a estimativa em BRL.</p></div>
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">2. Gere o PIX</p><p class="mt-2 text-sm leading-7 theme-muted">Ao confirmar, o sistema cria a cobranca PIX e redireciona para a visualizacao do pagamento.</p></div>
                <div class="rounded-[24px] p-5 theme-soft"><p class="text-sm font-semibold theme-text">3. Aguarde a compensacao</p><p class="mt-2 text-sm leading-7 theme-muted">Depois do pagamento confirmado, os creditos sao adicionados automaticamente a sua conta.</p></div>
            </div>
        </div>
        <div class="rounded-[32px] p-7 md:p-8 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">Atalhos</p>
            <div class="mt-5 space-y-3">
                <a href="{{ route('dashboard') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Voltar ao dashboard</a>
                <a href="{{ route('imei.index') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Abrir servicos IMEI</a>
                <a href="{{ route('server-services') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Abrir servicos Server</a>
            </div>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
