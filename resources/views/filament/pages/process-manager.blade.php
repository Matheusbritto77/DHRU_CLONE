<x-filament-panels::page>
    <div wire:poll.5s="refreshData">
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
                    <p class="text-sm mt-1">Certifique-se que o comando <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-primary-500">bun run manager.ts</code> está rodando.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
