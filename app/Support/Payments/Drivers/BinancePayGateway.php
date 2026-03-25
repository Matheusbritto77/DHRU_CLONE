<?php

namespace App\Support\Payments\Drivers;

use App\Models\Deposito;
use App\Models\User;
use App\Support\Payments\AbstractPaymentGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class BinancePayGateway extends AbstractPaymentGateway
{
    public function pluginSlug(): string
    {
        return 'payment-binance-pay';
    }

    public function label(): string
    {
        return 'Binance Pay';
    }

    public function createCheckout(array $payload, User $user): array
    {
        $this->requireEnabled();
        $settings = $this->settings();

        $apiKey = (string) ($settings['api_key'] ?? '');
        $apiSecret = (string) ($settings['api_secret'] ?? '');

        if ($apiKey === '' || $apiSecret === '') {
            throw new RuntimeException('Credenciais da Binance nao configuradas.');
        }

        $orderData = [
            'env' => ['terminalType' => 'APP'],
            'merchantTradeNo' => (string) Str::uuid(),
            'orderAmount' => (float) $payload['amount_usd_hidden'],
            'currency' => (string) ($settings['currency'] ?? 'USDT'),
            'description' => (string) ($settings['description'] ?? 'Recarga de creditos'),
            'returnUrl' => (string) ($settings['return_url'] ?? url('/add-credits')),
        ];

        $timestamp = now()->getTimestampMs();
        $signature = hash_hmac('sha512', $timestamp . "\n" . json_encode($orderData) . "\n", $apiSecret);

        $response = Http::withHeaders([
            'BinancePay-Timestamp' => $timestamp,
            'BinancePay-Nonce' => Str::random(32),
            'BinancePay-Certificate-SN' => $apiKey,
            'BinancePay-Signature' => $signature,
        ])->post('https://bpay.binanceapi.com/binancepay/openapi/v3/order', $orderData);

        $checkoutUrl = $response->json('data.checkoutUrl');

        if (! $response->successful() || blank($checkoutUrl)) {
            throw new RuntimeException('Falha ao criar checkout Binance Pay.');
        }

        Deposito::create([
            'txid' => $orderData['merchantTradeNo'],
            'gateway_slug' => $this->pluginSlug(),
            'gateway_reference' => (string) data_get($response->json(), 'data.prepayId', $orderData['merchantTradeNo']),
            'gateway_payload' => $response->json(),
            'valor' => (float) $payload['amount_usd_hidden'],
            'user_id' => $user->id,
            'status' => 0,
        ]);

        return [
            'type' => 'redirect',
            'target' => $checkoutUrl,
        ];
    }
}
