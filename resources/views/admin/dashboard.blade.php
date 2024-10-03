<x-app-layout layout="app2">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ADMINISTRATIVO') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Card de Total de Usuários Registrados -->
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Total de Usuários Registrados</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalUsuarios }}</p>
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>
