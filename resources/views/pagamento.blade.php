<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Depósito de Crédito') }}
        </h2>
    </x-slot>

    <style>
        /* Estilos personalizados */
        /* Pode adicionar estilos adicionais conforme necessário */
    </style>

    <div class="py-12">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                <div class="mt-8 text-2xl">
                    Depósito de Crédito
                </div>

                <div class="mt-6 text-gray-500 overflow-x-auto">
                    <form action="{{ route('processarPagamento') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Quantidade a ser depositada:</label>
                            <input type="text" class="form-control" id="amount" name="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Descrição do depósito:</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Depositar Crédito</button>
                    </form>
                </div>
            </div>

            <!-- Aqui você pode adicionar o restante do conteúdo da página, como o histórico de IMEI, por exemplo -->
        </div>
    </div>
</x-app-layout>
