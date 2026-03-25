<?php

namespace Database\Seeders\Plugins;

class DashboardStatsPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'dashboard-stats';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Cards de Estatisticas do Dashboard',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-chart-bar',
            'description' => 'Cards principais do dashboard com metricas da conta.',
            'default_settings' => [
                'credit_label' => 'Credito atual',
                'imei_label' => 'Ordens IMEI',
                'server_label' => 'Ordens Server',
            ],
            'blade_template' => <<<'BLADE'
@php
    $user = Auth::user();
    $imeiSuccessCount = \App\Models\ImeiService::where('user_id', $user->id)->where('status', 4)->count();
    $imeiRejectedCount = \App\Models\ImeiService::where('user_id', $user->id)->where('status', 3)->count();
    $serverSuccessCount = \App\Models\server_services::where('user_id', $user->id)->where('status', 4)->count();
    $serverRejectedCount = \App\Models\server_services::where('user_id', $user->id)->where('status', 3)->count();
    $totalIMEIOrders = \App\Models\ImeiService::where('user_id', $user->id)->count();
    $totalServerOrders = \App\Models\server_services::where('user_id', $user->id)->count();
@endphp

<section class="px-4 sm:px-6 lg:px-10 py-6 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="rounded-[30px] p-7 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['credit_label'] }}</p>
            <p class="mt-6 text-4xl font-semibold tracking-tight theme-text">${{ number_format($user->credit, 2) }}</p>
            <p class="mt-3 text-sm theme-muted">Saldo disponivel para novas ordens e operacoes.</p>
        </div>

        <div class="rounded-[30px] p-7 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['imei_label'] }}</p>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between text-sm theme-muted"><span>Sucesso</span><strong class="theme-text text-lg">{{ $imeiSuccessCount }}</strong></div>
                <div class="flex items-center justify-between text-sm theme-muted"><span>Rejeitadas</span><strong class="theme-text text-lg">{{ $imeiRejectedCount }}</strong></div>
                <div class="flex items-center justify-between text-sm theme-muted"><span>Total</span><strong class="theme-text text-lg">{{ $totalIMEIOrders }}</strong></div>
            </div>
        </div>

        <div class="rounded-[30px] p-7 theme-panel">
            <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['server_label'] }}</p>
            <div class="mt-6 space-y-3">
                <div class="flex items-center justify-between text-sm theme-muted"><span>Sucesso</span><strong class="theme-text text-lg">{{ $serverSuccessCount }}</strong></div>
                <div class="flex items-center justify-between text-sm theme-muted"><span>Rejeitadas</span><strong class="theme-text text-lg">{{ $serverRejectedCount }}</strong></div>
                <div class="flex items-center justify-between text-sm theme-muted"><span>Total</span><strong class="theme-text text-lg">{{ $totalServerOrders }}</strong></div>
            </div>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
