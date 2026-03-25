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
            'description' => 'Formulario principal para iniciar recarga via gateways de pagamento.',
            'default_settings' => [
                'title' => 'Recarga da conta',
                'usd_label' => 'Valor em USD',
                'total_label' => 'Total estimado',
                'payment_label' => 'Metodo de pagamento',
                'submit_text' => 'Ir para pagamento',
                'service_fee_rate' => '0.00',
                'minimum_usd' => '10',
            ],
            'blade_template' => <<<'BLADE'
<section class="px-4 sm:px-6 lg:px-10 py-6 lg:pl-[360px]">
    @php
        $paymentGateways = app(\App\Support\Payments\PaymentGatewayManager::class)->activeGateways();
        $defaultGateway = $paymentGateways[0]['slug'] ?? 'payment-gerencianet-pix';
        $defaultGatewayData = collect($paymentGateways)->firstWhere('slug', $defaultGateway);
        $userCurrency = strtoupper(auth()->user()?->preferred_currency ?: 'USD');
    @endphp
    <div class="max-w-6xl mx-auto">
        <div class="grid gap-5 lg:grid-cols-[1fr_0.8fr]">
            <div class="rounded-[32px] p-6 md:p-8 theme-panel">
                <p class="text-[11px] uppercase tracking-[0.3em] theme-muted">{{ $settings['title'] }}</p>
                <h2 class="mt-3 text-2xl font-semibold tracking-tight theme-text">Defina o valor e siga para o pagamento.</h2>

                <form id="credit-form-builder" action="{{ route('payments.checkout', ['gateway' => $defaultGateway]) }}" method="POST" class="mt-8 space-y-6">
                    @csrf
                    <div>
                        <label for="credit-amount-usd" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['usd_label'] }}</label>
                        <input type="number" id="credit-amount-usd" name="credit_amount" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text" required min="1" step="0.01" placeholder="Ex: 25">
                    </div>

                    <div>
                        <label for="credit-total" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['total_label'] }}</label>
                        <input type="text" id="credit-total" name="total" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none theme-soft theme-text" readonly>
                    </div>

                    <div>
                        <label for="credit-payment-method" class="mb-2 block text-sm font-medium theme-muted">{{ $settings['payment_label'] }}</label>
                        <select id="credit-payment-method" name="payment_method" class="w-full rounded-[18px] px-4 py-3 text-sm outline-none transition theme-soft theme-text">
                            @foreach ($paymentGateways as $gateway)
                                <option
                                    value="{{ $gateway['slug'] }}"
                                    data-checkout-currency="{{ $gateway['checkout_currency'] }}"
                                    data-gateway-fee-rate="{{ $gateway['gateway_fee_rate'] }}"
                                    data-gateway-fixed-fee="{{ $gateway['gateway_fixed_fee'] }}"
                                    data-manual="{{ $gateway['manual_checkout'] ? '1' : '0' }}"
                                >{{ $gateway['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" id="credit-global-fee-rate" name="global_fee_rate" value="{{ (float) $settings['service_fee_rate'] }}">

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
                        <p id="credit-conversion-hint" class="mt-2 text-sm leading-7 theme-muted">O gateway pode converter automaticamente de {{ $userCurrency }} para a moeda de cobranca.</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">Taxa operacional</p>
                        <p class="mt-2 text-sm leading-7 theme-muted">{{ number_format(((float) $settings['service_fee_rate']) * 100, 0) }}% sobre o valor convertido.</p>
                    </div>
                    <div class="rounded-[24px] p-5 theme-soft">
                        <p class="text-sm font-semibold theme-text">Moeda do gateway</p>
                        <p id="credit-gateway-currency" class="mt-2 text-sm leading-7 theme-muted">{{ $defaultGatewayData['checkout_currency'] ?? 'USD' }}</p>
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
    const creditFormBuilder = document.getElementById('credit-form-builder');
    const creditPaymentMethod = document.getElementById('credit-payment-method');
    const creditGatewayCurrency = document.getElementById('credit-gateway-currency');
    const creditConversionHint = document.getElementById('credit-conversion-hint');
    const creditServiceFeeRate = {{ (float) $settings['service_fee_rate'] }};
    const creditMinimumUsd = {{ (float) $settings['minimum_usd'] }};
    const currencyRates = @json(\App\Models\Currency::query()->where('is_active', true)->pluck('exchange_rate', 'code')->mapWithKeys(fn ($rate, $code) => [strtoupper($code) => (float) $rate])->all());
    const userCurrency = '{{ $userCurrency }}';

    function getRate(code) {
        const normalized = (code || 'USD').toUpperCase();
        return currencyRates[normalized] || 1;
    }

    function convertFromUsd(amount, targetCode) {
        return amount * getRate(targetCode);
    }

    function convertBetween(amount, fromCode, toCode) {
        if (fromCode === toCode) {
            return amount;
        }

        const amountInUsd = fromCode === 'USD' ? amount : amount / getRate(fromCode);
        return toCode === 'USD' ? amountInUsd : amountInUsd * getRate(toCode);
    }

    function calculateCreditTotal() {
        const amountUsd = parseFloat(creditAmountUsd.value || 0);
        const selectedOption = creditPaymentMethod.options[creditPaymentMethod.selectedIndex];
        const gatewayCurrency = (selectedOption?.dataset.checkoutCurrency || 'USD').toUpperCase();
        const gatewayFeeRate = parseFloat(selectedOption?.dataset.gatewayFeeRate || 0);
        const gatewayFixedFee = parseFloat(selectedOption?.dataset.gatewayFixedFee || 0);
        const subtotal = convertFromUsd(amountUsd, gatewayCurrency);
        const totalGateway = subtotal + (subtotal * creditServiceFeeRate) + (subtotal * gatewayFeeRate) + gatewayFixedFee;
        const totalUser = convertBetween(totalGateway, gatewayCurrency, userCurrency);

        creditTotal.value = totalUser > 0 ? `${totalUser.toFixed(2)} ${userCurrency}` : '';
        creditGatewayCurrency.textContent = gatewayCurrency;
        creditConversionHint.textContent = `1 USD = ${getRate(gatewayCurrency).toFixed(4)} ${gatewayCurrency}`;
    }

    creditAmountUsd.addEventListener('input', calculateCreditTotal);
    creditPaymentMethod.addEventListener('change', function () {
        const gateway = creditPaymentMethod.value || '{{ $defaultGateway }}';
        creditFormBuilder.action = `{{ url('/payments') }}/${gateway}/checkout`;
        calculateCreditTotal();
    });

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

    calculateCreditTotal();
</script>
BLADE,
        ];
    }
}
