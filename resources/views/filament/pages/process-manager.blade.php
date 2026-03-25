<x-filament-panels::page>
    <div wire:poll.5s="refreshData">
        <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex flex-col gap-2">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    Fila ativa: <span class="font-mono">{{ $queueConnection }}</span>
                </p>

                @if ($horizonAvailable)
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        O Horizon pode gerenciar os workers porque a fila atual usa Redis.
                    </p>
                @else
                    <p class="text-sm text-amber-700 dark:text-amber-400">
                        O Horizon exige Redis. Com a fila em <span class="font-mono">{{ $queueConnection }}</span>, este projeto continua usando <span class="font-mono">queue:work</span> no manager.
                    </p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($processes as $process)
                <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 transition-all hover:shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-500">
                                @if($process['key'] === 'queue')
                                    <x-heroicon-o-queue-list class="w-6 h-6" />
                                @elseif($process['key'] === 'schedule')
                                    <x-heroicon-o-clock class="w-6 h-6" />
                                @else
                                    <x-heroicon-o-arrow-path class="w-6 h-6" />
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $process['label'] }}</h3>
                                <p class="text-xs text-gray-400 font-mono truncate max-w-[150px]">{{ $process['command'] }}</p>
                            </div>
                        </div>
                        
                        <div @class([
                            'px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                            'bg-green-100 text-green-700 dark:bg-green-900/30' => $process['status'] === 'running',
                            'bg-red-100 text-red-700 dark:bg-red-900/30' => $process['status'] === 'stopped',
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 animate-pulse' => $process['status'] === 'starting',
                        ])>
                            {{ __($process['status']) }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        @if($process['status'] === 'stopped')
                            <x-filament::button 
                                wire:click="triggerProcessAction('{{ $process['key'] }}', 'start')"
                                color="success" size="sm" class="flex-1 rounded-xl">
                                Iniciar
                            </x-filament::button>
                        @else
                            <x-filament::button 
                                wire:click="triggerProcessAction('{{ $process['key'] }}', 'stop')"
                                color="danger" size="sm" class="flex-1 rounded-xl">
                                Parar
                            </x-filament::button>
                            
                            <x-filament::button 
                                wire:click="triggerProcessAction('{{ $process['key'] }}', 'restart')"
                                color="warning" size="sm" class="rounded-xl">
                                <x-heroicon-m-arrow-path class="w-4 h-4" />
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500">
                    <x-heroicon-o-signal-slash class="w-12 h-12 mx-auto mb-4 text-gray-300" />
                    <p class="text-lg font-medium">Servidor de Gerenciamento Offline</p>
                    <p class="text-sm mt-1">No container, confirme se o processo <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-primary-500">bun-manager</code> foi iniciado pelo Supervisor.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
