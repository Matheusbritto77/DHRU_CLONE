<?php

namespace App\Support\Payments\Drivers;

use App\Models\Deposito;
use App\Models\User;
use App\Support\Payments\AbstractPaymentGateway;
use App\Support\Payments\PaymentWebhookResult;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'orderAmount' => (float) ($payload['total_gateway'] ?? $payload['amount_usd_hidden']),
            'currency' => (string) ($payload['gateway_currency'] ?? $settings['currency'] ?? 'USDT'),
            'description' => (string) ($settings['description'] ?? 'Recarga de creditos'),
            'returnUrl' => (string) ($settings['return_url'] ?? url('/add-credits')),
            'webhookUrl' => route('payments.events.webhook', ['gateway' => $this->pluginSlug()]),
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
            'webhook_token' => $this->generateWebhookToken(),
            'gateway_payload' => $response->json(),
            'valor' => (float) $payload['amount_usd_hidden'],
            'user_id' => $user->id,
            'status' => 0,
            'payment_status_detail' => 'pending',
        ]);

        return [
            'type' => 'redirect',
            'target' => $checkoutUrl,
        ];
    }

    public function validateWebhook(Request $request): bool
    {
        $signature = (string) $request->header('BinancePay-Signature');
        $timestamp = (string) $request->header('BinancePay-Timestamp');
        $nonce = (string) $request->header('BinancePay-Nonce');
        $certificateSn = (string) $request->header('BinancePay-Certificate-SN');
        $apiKey = (string) ($this->settings()['api_key'] ?? '');
        $secret = (string) ($this->settings()['api_secret'] ?? '');

        if ($signature === '' || $timestamp === '' || $nonce === '' || $certificateSn === '' || $apiKey === '' || $secret === '') {
            return false;
        }

        if (! hash_equals($apiKey, $certificateSn)) {
            return false;
        }

        try {
            $age = abs(Carbon::createFromTimestampMs((int) $timestamp)->diffInSeconds(now()));
        } catch (\Throwable) {
            return false;
        }

        if ($age > 300) {
            return false;
        }

        $expected = strtoupper(hash_hmac('sha512', $timestamp . "\n" . $nonce . "\n" . $request->getContent() . "\n", $secret));

        return hash_equals($expected, strtoupper($signature));
    }

    public function handleWebhook(Request $request): PaymentWebhookResult
    {
        $payload = $request->all();

        return new PaymentWebhookResult(
            eventId: (string) data_get($payload, 'bizId', data_get($payload, 'data.bizId', Str::uuid()->toString())),
            eventType: (string) data_get($payload, 'bizType', 'binance.order'),
            status: $this->mapBinanceStatus((string) data_get($payload, 'data.status', 'pending')),
            payload: $payload,
            depositReference: (string) data_get($payload, 'data.merchantTradeNo', data_get($payload, 'merchantTradeNo')),
        );
    }

    public function findDepositFromWebhook(array $payload): ?Deposito
    {
        $reference = (string) data_get($payload, 'data.merchantTradeNo', data_get($payload, 'merchantTradeNo', ''));

        if ($reference === '') {
            return null;
        }

        return Deposito::query()
            ->where('gateway_slug', $this->pluginSlug())
            ->where('txid', $reference)
            ->first();
    }

    protected function mapBinanceStatus(string $status): string
    {
        return match (strtolower($status)) {
            'paid', 'success' => 'paid',
            'expired' => 'expired',
            'refunded' => 'refunded',
            'failed', 'error' => 'failed',
            default => 'pending',
        };
    }
}
