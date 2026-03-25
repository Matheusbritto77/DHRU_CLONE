<x-filament-panels::page>
    <div wire:poll.15s="refreshData" class="space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Entradas</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['entries'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Agregados</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['aggregates'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Exceptions</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['exceptions'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Slow jobs</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['slow_jobs'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Slow outgoing</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['slow_requests'] ?? 0 }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Tipos de evento</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($entryTypes as $item)
                        <div class="rounded-xl border p-3">
                            <div class="mb-2 flex items-center justify-between">
                                <span>{{ $item['type'] }}</span>
                                <span class="font-semibold">{{ $item['total'] }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800">
                                <div class="h-2 rounded-full bg-primary-500" style="width: {{ $item['width'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Slow jobs</h3>
                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                    @foreach ($slowJobsCards as $item)
                        <div class="rounded-xl border p-4">
                            <div class="text-sm text-gray-500">{{ $item['label'] }}</div>
                            <div class="mt-2 text-2xl font-semibold">{{ $item['max_ms'] }}ms</div>
                            <div class="mt-1 break-all text-xs text-gray-500">{{ $item['key'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Slow outgoing requests</h3>
                <div class="mt-4 grid grid-cols-1 gap-3">
                    @foreach ($slowRequestsCards as $item)
                        <div class="rounded-xl border p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="text-sm font-semibold">{{ $item['label'] }}</div>
                                <div class="text-lg font-semibold">{{ $item['max_ms'] }}ms</div>
                            </div>
                            <div class="mt-2 break-all text-xs text-gray-500">{{ $item['target'] }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $item['total'] }} ocorrencias</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Exceptions recentes</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="pb-3">Data</th>
                                <th class="pb-3">Exception</th>
                                <th class="pb-3">Hits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exceptions as $entry)
                                <tr class="border-t align-top">
                                    <td class="py-3 whitespace-nowrap">{{ $entry['recorded_at'] }}</td>
                                    <td class="py-3 break-all text-xs">{{ $entry['exception'] }}</td>
                                    <td class="py-3 whitespace-nowrap">{{ $entry['hits'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
            <h3 class="text-lg font-semibold">Outgoing requests lentas</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="pb-3">Data</th>
                            <th class="pb-3">Metodo</th>
                            <th class="pb-3">Destino</th>
                            <th class="pb-3">Duracao</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($outgoingRequests as $entry)
                            <tr class="border-t align-top">
                                <td class="py-3 whitespace-nowrap">{{ $entry['recorded_at'] }}</td>
                                <td class="py-3 whitespace-nowrap">{{ $entry['method'] }}</td>
                                <td class="py-3 break-all text-xs">{{ $entry['target'] }}</td>
                                <td class="py-3 whitespace-nowrap">{{ $entry['duration_ms'] }}ms</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
