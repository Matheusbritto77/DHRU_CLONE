<x-app-layout layout="app2">
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-white leading-tight tracking-tight">
            {{ __('Dashboard Administrativo') }}
        </h2>
    </x-slot>

    <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card de Total de Usuários -->
            <div class="bg-white dark:bg-[#1d1d1f] shadow-[0_4px_24px_rgba(0,0,0,0.04)] dark:shadow-[0_4px_24px_rgba(0,0,0,0.2)] rounded-[24px] p-8 border border-gray-100 dark:border-gray-800 transition-transform duration-300 hover:-translate-y-1">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-apple-blue/10 dark:bg-apple-blue/20 rounded-[14px] flex items-center justify-center text-apple-blue">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400 tracking-tight">Total de Usuários Registrados</h3>
                </div>
                <p class="text-[40px] font-bold text-apple-dark dark:text-white tracking-tighter">{{ $totalUsuarios }}</p>
            </div>
            
            <!-- Cards Fantasma Padrão (Sugestão de Expansão) -->
            <div class="bg-white dark:bg-[#1d1d1f] shadow-[0_4px_24px_rgba(0,0,0,0.02)] dark:shadow-[0_4px_24px_rgba(0,0,0,0.1)] rounded-[24px] p-8 border border-dashed border-gray-200 dark:border-gray-800/50 opacity-60">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-[14px] flex items-center justify-center text-gray-400">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-400 dark:text-gray-500 tracking-tight">Ordens Hoje</h3>
                </div>
                <p class="text-[40px] font-bold text-gray-300 dark:text-gray-700 tracking-tighter">--</p>
            </div>
        </div>
    </div>
</x-app-layout>
