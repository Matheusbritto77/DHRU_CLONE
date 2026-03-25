<x-filament-panels::page>
    <div wire:poll.10s="refreshData" class="space-y-6">
        @if ($error)
            <div class="rounded-2xl border border-danger-200 bg-danger-50 p-4 text-danger-700">
                {{ $error }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Status</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['status'] ?? 'inactive' }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Jobs/min</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['jobs_per_minute'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Recentes</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['recent_jobs'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Falhos recentes</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['failed_jobs'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Throughput</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['throughput'] ?? 0 }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Masters</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="pb-3">Nome</th>
                                <th class="pb-3">Ambiente</th>
                                <th class="pb-3">PID</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($masters as $master)
                                <tr class="border-t">
                                    <td class="py-3">{{ $master['name'] }}</td>
                                    <td class="py-3">{{ $master['environment'] }}</td>
                                    <td class="py-3 font-mono">{{ $master['pid'] }}</td>
                                    <td class="py-3">{{ $master['status'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-gray-500">Sem masters ativos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Filas</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="pb-3">Fila</th>
                                <th class="pb-3">Pendentes</th>
                                <th class="pb-3">Espera</th>
                                <th class="pb-3">Processos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($workload as $queue)
                                <tr class="border-t">
                                    <td class="py-3">{{ $queue['name'] }}</td>
                                    <td class="py-3">{{ $queue['length'] }}</td>
                                    <td class="py-3">{{ $queue['wait'] }}s</td>
                                    <td class="py-3">{{ $queue['processes'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-4 text-gray-500">Sem workload disponível.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Jobs Recentes</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($recentJobs as $job)
                        <div class="rounded-xl border p-3">
                            <div class="font-medium">{{ $job['name'] }}</div>
                            <div class="mt-1 text-xs text-gray-500">Fila {{ $job['queue'] }} · {{ $job['status'] }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Nenhum job recente.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <h3 class="text-lg font-semibold">Jobs Falhos</h3>
                <div class="mt-4 space-y-3">
                    @forelse ($failedJobs as $job)
                        <div class="rounded-xl border p-3">
                            <div class="font-medium">{{ $job['name'] }}</div>
                            <div class="mt-1 text-xs text-gray-500">Fila {{ $job['queue'] }} · {{ $job['failed_at'] ?: 'sem timestamp' }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Nenhum job falho recente.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
