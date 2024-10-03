<x-app-layout layout="app2">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <span class="font-bold">Histórico de IMEI</span>
        </h2>
    </x-slot>

    <style>
        .status-span {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.375rem;
            border-width: 1px;
            display: inline-flex;
            align-items: center;
        }

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
    </style>

    <div class="py-12">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                <div class="mt-8 text-2xl text-black">
                    <span class="font-bold">Histórico de IMEI</span>
                </div>

                <div class="mt-6 text-gray-500 overflow-x-auto overflow-y-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Código
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    IMEI
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    ID de Referência
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Custo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    ID do Usuário
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Nome do Serviço
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">
                                    Quantidade
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @switch($order['status'])
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order['code'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order['IMEI'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order['referenceid'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order['cost'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order['user_id'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal break-words text-sm">
                                        {{ $order['servicename'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $order['Qnt'] }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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
