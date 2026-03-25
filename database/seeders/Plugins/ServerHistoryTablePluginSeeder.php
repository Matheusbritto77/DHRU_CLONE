<?php

namespace Database\Seeders\Plugins;

class ServerHistoryTablePluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'server-history-table';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Tabela Historico Server',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-table-cells',
            'description' => 'Tabela principal do historico server.',
            'default_settings' => [
                'title' => 'Pedidos Server',
            ],
            'blade_template' => <<<'BLADE'
@php
    $statusMap = [
        0 => ['Preparando', '#dbeafe', '#1d4ed8'],
        1 => ['Processando', '#fef3c7', '#b45309'],
        2 => ['Processando', '#fef3c7', '#b45309'],
        3 => ['Rejeitado', '#fee2e2', '#b91c1c'],
        4 => ['Sucesso', '#dcfce7', '#15803d'],
    ];
@endphp

<section class="px-4 sm:px-6 lg:px-10 py-6 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="rounded-[32px] p-6 md:p-8 theme-panel">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
                    <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Historico completo das suas ordens server.</h2>
                </div>
                <a href="{{ route('server-services') }}" class="rounded-full px-5 py-3 text-sm font-medium shadow-sm theme-accent-bg">Novo pedido Server</a>
            </div>

            <div class="mt-8 overflow-hidden rounded-[28px] border" style="border-color: var(--theme-border);">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead style="background: var(--theme-surface-soft);">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Status</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Servico</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Qnt</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Custo</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Referencia</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">IMEI</th>
                                <th class="px-6 py-4 text-left text-[11px] font-semibold uppercase tracking-[0.28em] theme-muted">Codigo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serverOrders as $order)
                                @php([$statusLabel, $statusBg, $statusColor] = $statusMap[$order->status] ?? ['Desconhecido', '#e5e7eb', '#4b5563'])
                                <tr style="border-top: 1px solid var(--theme-border);">
                                    <td class="px-6 py-4 text-sm">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold" style="background: {{ $statusBg }}; color: {{ $statusColor }}; border-color: {{ $statusColor }}33;">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium theme-text">{{ $order->servicename }}</td>
                                    <td class="px-6 py-4 text-sm theme-muted">{{ $order->Qnt }}</td>
                                    <td class="px-6 py-4 text-sm theme-muted">{{ $order->cost }}</td>
                                    <td class="px-6 py-4 text-sm theme-muted">{{ $order->referenceid }}</td>
                                    <td class="px-6 py-4 text-sm theme-muted">{{ $order->IMEI }}</td>
                                    <td class="px-6 py-4 text-sm theme-muted">{{ $order->code }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-sm theme-muted">Nenhuma ordem encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (method_exists($serverOrders, 'links'))
                <div class="mt-6">
                    {{ $serverOrders->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
