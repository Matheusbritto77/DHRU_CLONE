<?php

namespace Database\Seeders\Plugins;

class AddCreditsHeroPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'add-credits-hero';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Hero Adicionar Creditos',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-banknotes',
            'description' => 'Cabecalho da pagina de adicionar creditos.',
            'default_settings' => [
                'eyebrow' => 'Adicionar creditos',
                'title' => 'Recarga simples, direta e sem ruido.',
                'description' => 'Informe o valor em USD, veja a conversao estimada e siga para o pagamento via PIX em um fluxo mais claro.',
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
                        <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Metodo</p>
                        <p class="mt-3 text-2xl font-semibold theme-text">PIX</p>
                    </div>
                    <div class="rounded-[24px] px-5 py-4 theme-soft">
                        <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Minimo</p>
                        <p class="mt-3 text-2xl font-semibold theme-text">10 USD</p>
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
