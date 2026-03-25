<x-filament-panels::page>
    <div wire:poll.15s="refreshData" class="space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
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
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Tipos de evento</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($entryTypes as $item)
                        <div class="flex items-center justify-between rounded-xl border p-3">
                            <span>{{ $item['type'] }}</span>
                            <span class="font-semibold">{{ $item['total'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Agregados recentes</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="pb-3">Hora</th>
                                <th class="pb-3">Tipo</th>
                                <th class="pb-3">Aggregate</th>
                                <th class="pb-3">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($queueAggregates as $item)
                                <tr class="border-t">
                                    <td class="py-3">{{ $item['bucket_at'] }}</td>
                                    <td class="py-3">{{ $item['type'] }}</td>
                                    <td class="py-3">{{ $item['aggregate'] }}</td>
                                    <td class="py-3">{{ $item['value'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
            <h3 class="text-lg font-semibold">Últimas entradas</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="pb-3">Data</th>
                            <th class="pb-3">Tipo</th>
                            <th class="pb-3">Key</th>
                            <th class="pb-3">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($latestEntries as $entry)
                            <tr class="border-t align-top">
                                <td class="py-3 whitespace-nowrap">{{ $entry['recorded_at'] }}</td>
                                <td class="py-3 whitespace-nowrap">{{ $entry['type'] }}</td>
                                <td class="py-3 break-all text-xs">{{ $entry['key'] }}</td>
                                <td class="py-3 whitespace-nowrap">{{ $entry['value'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
