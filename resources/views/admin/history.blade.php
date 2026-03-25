<x-app-layout layout="app2">
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-apple-dark dark:text-white leading-tight tracking-tight">
            Histórico de IMEI
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-[1400px] mx-auto">
        <div class="bg-white dark:bg-[#1d1d1f] overflow-hidden shadow-[0_8px_30px_rgba(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgba(0,0,0,0.2)] sm:rounded-[24px] border border-gray-100 dark:border-gray-800">
            <div class="p-6 sm:px-10 sm:py-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-[22px] font-semibold text-apple-dark dark:text-white tracking-tight">Ordens Recentes</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800/80">
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Status</th>
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Código</th>
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">IMEI</th>
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">ID Ref</th>
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Custo</th>
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Usuário</th>
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Serviço</th>
                                <th class="pb-4 px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Qtd</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/40">
                            @forelse ($orders ?? [] as $order)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @switch($order['status'])
                                            @case(0)
                                                <span class="px-3 py-1 font-medium rounded-full bg-blue-50 dark:bg-blue-900/30 text-apple-blue dark:text-blue-400 border border-blue-100 dark:border-blue-800/50 text-[13px]">Preparando</span>
                                                @break
                                            @case(1)
                                            @case(2)
                                                <span class="px-3 py-1 font-medium rounded-full bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400 border border-yellow-100 dark:border-yellow-800/50 text-[13px]">Processando</span>
                                                @break
                                            @case(3)
                                                <span class="px-3 py-1 font-medium rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800/50 text-[13px]">Rejeitado</span>
                                                @break
                                            @case(4)
                                                <span class="px-3 py-1 font-medium rounded-full bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800/50 text-[13px]">Sucesso</span>
                                                @break
                                            @default
                                                <span class="px-3 py-1 font-medium rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 text-[13px]">Desconhecido</span>
                                        @endswitch
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap text-[14px] font-medium text-apple-dark dark:text-gray-200">{{ $order['code'] }}</td>
                                    <td class="py-4 px-4 whitespace-nowrap text-[14px] text-gray-500 dark:text-gray-400 font-mono">{{ $order['IMEI'] }}</td>
                                    <td class="py-4 px-4 whitespace-nowrap text-[14px] text-gray-500 dark:text-gray-400">{{ $order['referenceid'] }}</td>
                                    <td class="py-4 px-4 whitespace-nowrap text-[14px] font-medium text-green-600 dark:text-green-400">${{ $order['cost'] }}</td>
                                    <td class="py-4 px-4 whitespace-nowrap text-[14px] text-gray-500 dark:text-gray-400 text-center">#{{ $order['user_id'] }}</td>
                                    <td class="py-4 px-4 text-[14px] text-gray-600 dark:text-gray-300 max-w-xs truncate" title="{{ $order['servicename'] }}">{{ $order['servicename'] }}</td>
                                    <td class="py-4 px-4 whitespace-nowrap text-[14px] text-gray-500 dark:text-gray-400 text-center">{{ $order['Qnt'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-12 px-4 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-inbox text-3xl mb-3 opacity-20"></i>
                                            <p class="font-light">Nenhuma ordem encontrada.</p>
                                        </div>
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
