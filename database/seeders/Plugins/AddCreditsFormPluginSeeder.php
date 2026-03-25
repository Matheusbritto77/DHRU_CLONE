<?php

namespace Database\Seeders\Plugins;

class AddCreditsFormPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'add-credits-form';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Formulario Adicionar Creditos',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-credit-card',
            'description' => 'Formulario principal para iniciar recarga via PIX.',
            'default_settings' => [
                'title' => 'Recarga da conta',
                'usd_label' => 'Valor em USD',
                'total_label' => 'Total estimado em BRL',
                'payment_label' => 'Metodo de pagamento',
                'submit_text' => 'Gerar cobranca PIX',
                'exchange_rate' => '5.60',
                'service_fee_rate' => '0.13',
                'minimum_usd' => '10',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 py-6 lg:pl-[360px]">
    <div class="max-w-6xl mx-auto">
        <div class="grid gap-5 lg:grid-cols-[1fr_0.8fr]">
            <div class="rounded-[32px] p-6 md:p-8 theme-panel">
                <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Defina o valor e siga para o pagamento.</h2>

                <form id="credit-form-builder" action="{{ route('process.pix') }}" method="POST" class="mt-8 space-y-6">
                    @csrf
                    <div>
                        <label for="credit-amount-usd" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['usd_label'] }}</label>
                        <input type="number" id="credit-amount-usd" name="amount_usd" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" required min="1" step="0.01" placeholder="Ex: 25">
                    </div>

                    <div>
                        <label for="credit-total" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['total_label'] }}</label>
                        <input type="text" id="credit-total" name="total" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none theme-soft theme-text" readonly>
                    </div>

                    <div>
                        <label for="credit-payment-method" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['payment_label'] }}</label>
                        <select id="credit-payment-method" name="payment_method" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text">
                            <option value="pix">PIX</option>
                        </select>
                    </div>

                    <input type="hidden" id="credit-total-brl" name="total_brl" value="">
                    <input type="hidden" id="credit-amount-usd-hidden" name="amount_usd_hidden" value="">

                    <button type="submit" class="rounded-full px-5 py-3 text-sm font-medium shadow-sm theme-accent-bg">
                        {{ $settings['submit_text'] }}
                    </button>
                </form>
            </div>

            <div class="rounded-[32px] p-6 md:p-8 theme-panel">
                <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">Resumo</p>
                <div class="mt-6 space-y-4">
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">Conversao usada</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">1 USD = {{ $settings['exchange_rate'] }} BRL</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">Taxa operacional</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">{{ number_format(((float) $settings['service_fee_rate']) * 100, 0) }}% sobre o valor convertido.</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">Deposito minimo</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">{{ $settings['minimum_usd'] }} USD para iniciar a cobranca.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const creditAmountUsd = document.getElementById('credit-amount-usd');
    const creditTotal = document.getElementById('credit-total');
    const creditTotalBrl = document.getElementById('credit-total-brl');
    const creditAmountUsdHidden = document.getElementById('credit-amount-usd-hidden');
    const creditFormBuilder = document.getElementById('credit-form-builder');
    const creditExchangeRate = {{ (float) $settings['exchange_rate'] }};
    const creditServiceFeeRate = {{ (float) $settings['service_fee_rate'] }};
    const creditMinimumUsd = {{ (float) $settings['minimum_usd'] }};

    function calculateCreditTotal() {
        const amountUsd = parseFloat(creditAmountUsd.value || 0);
        const amountBrl = amountUsd * creditExchangeRate;
        const serviceFee = amountBrl * creditServiceFeeRate;
        const total = amountBrl + serviceFee;

        creditTotal.value = total > 0 ? total.toFixed(2) : '';
        creditTotalBrl.value = total > 0 ? total.toFixed(2) : '';
        creditAmountUsdHidden.value = amountUsd > 0 ? amountUsd.toFixed(2) : '';
    }

    creditAmountUsd.addEventListener('input', calculateCreditTotal);

    creditFormBuilder.addEventListener('submit', function (event) {
        const amountUsd = parseFloat(creditAmountUsd.value || 0);
        if (amountUsd < creditMinimumUsd) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: `O deposito minimo e de ${creditMinimumUsd} dolares.`,
            });
        }
    });
</script>
BLADE,
        ];
    }
}
