<?php

namespace Database\Seeders\Plugins;

class ImeiServicesHelpPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'imei-services-help';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Auxiliar IMEI Services',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-information-circle',
            'description' => 'Bloco auxiliar com orientacoes rapidas para uso dos servicos IMEI.',
            'default_settings' => [
                'title' => 'Antes de enviar um pedido',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 pb-12 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="grid gap-5 lg:grid-cols-[1.3fr_0.7fr]">
            <div class="rounded-[32px] p-7 md:p-8 theme-panel">
                <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Fluxo mais claro, menos erro operacional.</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">1. Verifique o campo solicitado</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">Alguns servicos pedem serial, outros exigem campos extras. Leia o titulo do modal antes de enviar.</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">2. Confirme o custo</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">O valor e debitado do saldo do usuario no momento em que a ordem e enviada com sucesso.</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">3. Acompanhe no historico</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">Depois do envio, acompanhe o status em Historico IMEI para verificar processamento e retorno.</p>
                    </div>
                </div>
            </div>
            <div class="rounded-[32px] p-7 md:p-8 theme-panel">
                <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">Atalhos</p>
                <div class="mt-5 space-y-3">
                    <a href="{{ route('imei.history') }}" class="block rounded-[22px] px-5 py-4 theme-soft theme-text">Ver historico IMEI</a>
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
