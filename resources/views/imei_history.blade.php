<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <span class="font-bold">Histórico de IMEI</span>
        </h2>
    </x-slot>

    <style>
        /* Estilos personalizados */
        .status-span {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
            border-width: 1px;
            display: inline-flex;
            align-items: center;
        }

        /* Cores para diferentes status */
        .status-span-blue {
            background-color: #E0EBFF;
            color: #2B6CB0;
            border-color: #2B6CB0;
        }

        .status-span-yellow {
            background-color: #FEF3C7;
            color: #D69E2E;
            border-color: #D69E2E;
        }

        .status-span-red {
            background-color: #FEE2E2;
            color: #C53030;
            border-color: #C53030;
        }

        .status-span-green {
            background-color: #D1FAE5;
            color: #065F46;
            border-color: #065F46;
        }

        .status-span-gray {
            background-color: #F3F4F6;
            color: #4B5563;
            border-color: #4B5563;
        }

        /* Tabela ajustada */
        .overflow-x-auto {
            overflow-x: auto;
        }

        @media screen and (max-width: 640px) {
            .overflow-x-auto table {
                display: block;
                white-space: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .overflow-x-auto table th,
            .overflow-x-auto table td {
                min-width: 150px;
                padding: 10px;
            }

            .overflow-x-auto table th {
                background-color: #edf2f7; /* Cor de fundo do cabeçalho da tabela */
                font-size: 0.9rem; /* Tamanho da fonte do cabeçalho da tabela */
                color: #000000; /* Cor do texto do cabeçalho da tabela (preto) */
                border-bottom: 2px solid #cbd5e0; /* Linha divisória abaixo do cabeçalho da tabela */
            }

            .overflow-x-auto table td {
                border-bottom: 1px solid #cbd5e0; /* Linha divisória entre as células */
                color: #ffffff; /* Cor do texto das células */
            }
        }
    </style>

    <div class="py-12">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:px-20 bg-white border-b border-white-200">
                <div class="mt-8 text-2xl text-white">
                    <span class="font-bold">Histórico de IMEI</span>
                </div>

                <div class="mt-6 text-gray-500 overflow-x-auto overflow-y-hidden">
                    <table class="min-w-full divide-y divide-gray-200 overflow-x-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Serviço
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Custo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    ID de Referência
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    IMEI
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Código
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Resposta da API
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($imeiOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @switch($order->status)
                                            @case(0)
                                                <span class="status-span status-span-blue">Preparando</span>
                                                @break
                                            @case(1)
                                            @case(2)
                                                <span class="status-span status-span-yellow">Processando</span>
                                                @break
                                            @case(3)
                                                <span class="status-span status-span-red">Rejeitado</span>
                                                @break
                                            @case(4)
                                                <span class="status-span status-span-green">Sucesso</span>
                                                @break
                                            @default
                                                <span class="status-span status-span-gray">Desconhecido</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal break-words text-sm">
                                        {{ $order->servicename }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order->cost }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order->referenceid }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order->IMEI }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-wrap break-word text-sm">
                                        {{ $order->code }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-wrap break-word text-sm">
                                        <div>{!! $order->api_response !!}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        Nenhuma ordem encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
