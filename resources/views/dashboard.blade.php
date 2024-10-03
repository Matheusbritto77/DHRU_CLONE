<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-300 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Content -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card: Crédito Atual -->
                <div class="bg-gray-800 shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-300">Crédito Atual</h3>
                    <p class="mt-2 text-2xl text-gray-200">{{ $totalCredit }}</p>
                </div>

                <!-- Card: Ordens de IMEI -->
                <div class="bg-gray-800 shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-300">Ordens de IMEI</h3>
                    <p class="mt-2 text-sm text-gray-400">Sucesso: <span class="text-lg text-gray-200">{{ $imeiSuccessCount }}</span></p>
                    <p class="mt-2 text-sm text-gray-400">Rejeitadas: <span class="text-lg text-gray-200">{{ $imeiRejectedCount }}</span></p>
                    <p class="mt-2 text-sm text-gray-400">Total de Ordens: <span class="text-lg text-gray-200">{{ $totalIMEIOrders }}</span></p>
                </div>

                <!-- Card: Ordens de Servidor -->
                <div class="bg-gray-800 shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-300">Ordens de Servidor</h3>
                    <p class="mt-2 text-sm text-gray-400">Sucesso: <span class="text-lg text-gray-200">{{ $serverSuccessCount }}</span></p>
                    <p class="mt-2 text-sm text-gray-400">Rejeitadas: <span class="text-lg text-gray-200">{{ $serverRejectedCount }}</span></p>
                    <p class="mt-2 text-sm text-gray-400">Total de Ordens: <span class="text-lg text-gray-200">{{ $totalServerOrders }}</span></p>
                </div>
            </div>
        </div>
     
        
    </div>
   
</x-app-layout>

