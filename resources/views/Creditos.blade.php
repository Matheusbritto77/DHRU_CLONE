<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Adicionar Créditos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Formulário para adicionar créditos -->
                    <form id="creditForm" action="{{ route('process.pix') }}" method="POST" onsubmit="return validateForm()">
                        @csrf
                        <div class="mb-4">
                            <label for="amount_usd" class="block text-white-700">Valor em USD mínimo 10</label>
                            <input type="number" id="amount_usd" name="amount_usd" class="mt-1 text-black block w-full" required min="1" step="0.01" onchange="calculateTotal()">
                        </div>
                        <div class="mb-4">
                            <label for="total" class="block text-white-700">Total (em BRL)</label>
                            <input type="text" id="total" name="total" class="mt-1 block w-full text-black border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" readonly>
                        </div>
                        
                        <!-- Dropdown para método de pagamento -->
                        <div class="mb-4">
                            <label for="payment_method" class="block text-white-700">Método de Pagamento</label>
                            <select id="payment_method" name="payment_method" class="mt-1 block w-full text-black border-white-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="pix">PIX ✅</option>
                                <!-- Adicione mais opções conforme necessário -->
                            </select>
                        </div>

                        <!-- Campo oculto para enviar o total em BRL -->
                        <input type="hidden" id="total_brl" name="total_brl" value="">
                        <!-- Campo oculto para enviar o total em USD -->
                        <input type="hidden" id="amount_usd_hidden" name="amount_usd_hidden" value="">

                        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Adicionar</button>
                    </form>

                    <!-- Script para cálculo dinâmico do total -->
                    
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        // Função para calcular o total baseado no valor em USD inserido pelo usuário
                        function calculateTotal() {
                            let amountUsd = parseFloat(document.getElementById('amount_usd').value);
                            if (isNaN(amountUsd)) {
                                amountUsd = 0;
                            }

                            // Taxa de câmbio fixa (5.50 BRL por 1 USD)
                            let exchangeRate = 5.60;

                            // Calcula o valor em BRL
                            let amountBrl = amountUsd * exchangeRate;

                            // Calcula a taxa de serviço (2% sobre o valor em BRL)
                            let serviceFee = amountBrl * 0.13;

                            // Calcula o total
                            let total = amountBrl + serviceFee;

                            // Atualiza o campo de total na interface
                            document.getElementById('total').value = total.toFixed(2); // Formata para duas casas decimais

                            // Atualiza o campo oculto com o valor total em BRL
                            document.getElementById('total_brl').value = total.toFixed(2);

                            // Atualiza o campo oculto com o valor em USD
                            document.getElementById('amount_usd_hidden').value = amountUsd.toFixed(2);
                        }

                        // Função para validar o formulário antes de enviar
                        function validateForm() {
                            let amountUsd = parseFloat(document.getElementById('amount_usd').value);
                            if (amountUsd < 10) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: 'O depósito mínimo é de 10 dólares.',
                                });
                                return false; // Impede o envio do formulário
                            }
                            return true; // Permite o envio do formulário
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
