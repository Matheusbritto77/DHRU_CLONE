<?php

namespace App\Support\Payments;

use App\Models\Currency;
use App\Models\User;

class PaymentQuoteService
{
    /**
     * @param  array<string, mixed>  $gatewaySettings
     * @return array<string, mixed>
     */
    public function buildQuote(float $creditAmount, ?User $user, array $gatewaySettings = []): array
    {
        $userCurrency = strtoupper($user?->preferred_currency ?: config('app.currency_base', 'USD'));
        $gatewayCurrency = strtoupper((string) ($gatewaySettings['checkout_currency'] ?? $userCurrency));
        $globalFeeRate = (float) ($gatewaySettings['global_fee_rate'] ?? 0);
        $gatewayFeeRate = (float) ($gatewaySettings['gateway_fee_rate'] ?? 0);
        $gatewayFixedFee = (float) ($gatewaySettings['gateway_fixed_fee'] ?? 0);

        $subtotalInGatewayCurrency = $this->convert($creditAmount, 'USD', $gatewayCurrency);
        $globalFeeAmount = $subtotalInGatewayCurrency * $globalFeeRate;
        $gatewayFeeAmount = ($subtotalInGatewayCurrency * $gatewayFeeRate) + $gatewayFixedFee;
        $totalInGatewayCurrency = $subtotalInGatewayCurrency + $globalFeeAmount + $gatewayFeeAmount;
        $displayTotal = $this->convert($totalInGatewayCurrency, $gatewayCurrency, $userCurrency);

        return [
            'credit_amount' => round($creditAmount, 2),
            'base_currency' => 'USD',
            'user_currency' => $userCurrency,
            'gateway_currency' => $gatewayCurrency,
            'exchange_rate' => $this->rate($gatewayCurrency, 'USD'),
            'display_exchange_rate' => $this->rate($userCurrency, 'USD'),
            'subtotal_gateway' => round($subtotalInGatewayCurrency, 2),
            'global_fee_rate' => $globalFeeRate,
            'gateway_fee_rate' => $gatewayFeeRate,
            'gateway_fixed_fee' => $gatewayFixedFee,
            'global_fee_amount' => round($globalFeeAmount, 2),
            'gateway_fee_amount' => round($gatewayFeeAmount, 2),
            'total_gateway' => round($totalInGatewayCurrency, 2),
            'display_total' => round($displayTotal, 2),
        ];
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);

        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $fromRate = $this->rate($fromCurrency, 'USD');
        $toRate = $this->rate($toCurrency, 'USD');

        if ($fromRate <= 0 || $toRate <= 0) {
            return $amount;
        }

        $amountInBase = $fromCurrency === 'USD' ? $amount : ($amount / $fromRate);

        return $toCurrency === 'USD' ? $amountInBase : ($amountInBase * $toRate);
    }

    public function rate(string $currencyCode, string $baseCurrency = 'USD'): float
    {
        $currencyCode = strtoupper($currencyCode);
        $baseCurrency = strtoupper($baseCurrency);

        if ($currencyCode === $baseCurrency) {
            return 1.0;
        }

        $currency = Currency::query()->where('code', $currencyCode)->first();

        return (float) ($currency?->exchange_rate ?? 1.0);
    }
}
