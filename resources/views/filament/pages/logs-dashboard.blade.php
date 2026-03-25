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
                        <button
                            type="button"
                            wire:click="$set('selectedFile', '{{ $file['path'] }}'); refreshData()"
                            class="block w-full rounded-xl border p-3 text-left transition hover:border-primary-400"
                        >
                            <div class="font-medium">{{ $file['name'] }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $file['size_kb'] }} KB · {{ $file['updated_at'] }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border bg-white p-5 shadow-sm dark:bg-gray-900 xl:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Tail recente</h3>
                    <div class="text-xs text-gray-500">{{ basename($selectedFile ?: '-') }}</div>
                </div>
                <div class="mt-4">
                    {{ $this->table }}
                </div>
            </div>
        </div>

        @if ($selectedEntry)
            <div class="fixed inset-0 z-50 flex justify-end bg-black/40">
                <div class="h-full w-full max-w-3xl overflow-y-auto bg-white p-6 shadow-2xl dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Detalhe do log</h3>
                            <p class="text-sm text-gray-500">{{ $selectedEntry['level'] ?? 'info' }}</p>
                        </div>
                        <x-filament::button color="gray" wire:click="$set('selectedEntry', null)">Fechar</x-filament::button>
                    </div>

                    <pre class="mt-6 whitespace-pre-wrap break-words rounded-2xl border bg-gray-50 p-4 text-xs dark:bg-gray-950">{{ $selectedEntry['message'] ?? '' }}</pre>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
