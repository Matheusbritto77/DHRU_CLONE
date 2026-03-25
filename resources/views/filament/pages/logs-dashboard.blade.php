<x-filament-panels::page>
    <div wire:poll.10s="refreshData" class="space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Arquivos</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['files'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Arquivo ativo</div>
                <div class="mt-2 text-sm font-semibold">{{ $stats['latest_file'] ?? '-' }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Erros no tail</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['errors'] ?? 0 }}</div>
            </div>
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900">
                <div class="text-sm text-gray-500">Warnings no tail</div>
                <div class="mt-2 text-2xl font-semibold">{{ $stats['warnings'] ?? 0 }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900 xl:col-span-1">
                <h3 class="text-lg font-semibold">Arquivos</h3>
                <div class="mt-4 space-y-3">
                    @foreach ($files as $file)
                        <div class="rounded-xl border p-3">
                            <div class="font-medium">{{ $file['name'] }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $file['size_kb'] }} KB · {{ $file['updated_at'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900 xl:col-span-2">
                <h3 class="text-lg font-semibold">Tail recente</h3>
                <div class="mt-4 space-y-2">
                    @forelse ($entries as $entry)
                        <div @class([
                            'rounded-xl border p-3 font-mono text-xs break-all',
                            'border-danger-200 bg-danger-50 dark:border-danger-900/30 dark:bg-danger-950/20' => in_array($entry['level'], ['error', 'critical', 'alert', 'emergency'], true),
                            'border-warning-200 bg-warning-50 dark:border-warning-900/30 dark:bg-warning-950/20' => $entry['level'] === 'warning',
                            'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800' => ! in_array($entry['level'], ['error', 'critical', 'alert', 'emergency', 'warning'], true),
                        ])>
                            {{ $entry['message'] }}
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Nenhuma linha encontrada.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
