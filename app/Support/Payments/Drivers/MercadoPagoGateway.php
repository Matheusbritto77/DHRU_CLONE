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

class MercadoPagoGateway extends AbstractPaymentGateway
{
    public function pluginSlug(): string
    {
        return 'payment-mercado-pago';
    }

    public function label(): string
    {
        return 'Mercado Pago';
    }

    public function createCheckout(array $payload, User $user): array
    {
        $this->requireEnabled();

        $settings = $this->settings();
        $accessToken = (string) ($settings['access_token'] ?? '');

        if ($accessToken === '') {
            throw new RuntimeException('Credenciais do Mercado Pago nao configuradas.');
        }

        $reference = (string) Str::uuid();
        $currency = strtolower((string) ($settings['checkout_currency'] ?? 'BRL'));

        $response = Http::withToken($accessToken)
            ->post('https://api.mercadopago.com/checkout/preferences', [
                'external_reference' => $reference,
                'notification_url' => route('payments.events.webhook', ['gateway' => $this->pluginSlug()]),
                'items' => [[
                    'id' => $reference,
                    'title' => 'Recarga de creditos',
                    'quantity' => 1,
                    'currency_id' => strtoupper($currency),
                    'unit_price' => (float) $payload['total_brl'],
                ]],
                'back_urls' => [
                    'success' => (string) ($settings['success_url'] ?? url('/add-credits')),
                    'failure' => (string) ($settings['failure_url'] ?? url('/add-credits')),
                    'pending' => (string) ($settings['pending_url'] ?? url('/add-credits')),
                ],
                'auto_return' => 'approved',
            ]);

        $checkoutUrl = $response->json('init_point') ?: $response->json('sandbox_init_point');

        if (! $response->successful() || blank($checkoutUrl)) {
            throw new RuntimeException('Falha ao criar checkout Mercado Pago.');
        }

        Deposito::create([
            'txid' => $reference,
            'gateway_slug' => $this->pluginSlug(),
            'gateway_reference' => (string) ($response->json('id') ?? $reference),
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
        $secret = (string) ($this->settings()['webhook_secret'] ?? '');
        $signatureHeader = (string) $request->header('x-signature');
        $requestId = (string) $request->header('x-request-id');

        if ($secret === '' || $signatureHeader === '' || $requestId === '') {
            return false;
        }

        $signatureParts = collect(explode(',', $signatureHeader))
            ->map(fn (string $part): array => array_pad(explode('=', trim($part), 2), 2, null))
            ->filter(fn (array $pair): bool => filled($pair[0]) && filled($pair[1]))
            ->mapWithKeys(fn (array $pair): array => [trim((string) $pair[0]) => trim((string) $pair[1])]);

        $timestamp = $signatureParts->get('ts');
        $hash = $signatureParts->get('v1');

        if (! $timestamp || ! $hash) {
            return false;
        }

        try {
            $age = abs(Carbon::createFromTimestamp((int) $timestamp)->diffInSeconds(now()));
        } catch (\Throwable) {
            return false;
        }

        if ($age > 300) {
            return false;
        }

        $dataId = (string) data_get($request->all(), 'data.id', $request->query('data.id', ''));

        if ($dataId === '') {
            return false;
        }

        $manifest = sprintf(
            'id:%s;request-id:%s;ts:%s;',
            strtolower($dataId),
            $requestId,
            $timestamp,
        );

        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $hash);
    }

    public function handleWebhook(Request $request): PaymentWebhookResult
    {
        $payload = $request->all();
        $accessToken = (string) ($this->settings()['access_token'] ?? '');

        $resourceUrl = (string) data_get($payload, 'resource', data_get($payload, 'data.id'));
        $paymentData = [];

        if ($resourceUrl !== '') {
            $resourceUrl = str_starts_with($resourceUrl, 'http')
                ? $resourceUrl
                : 'https://api.mercadopago.com/v1/payments/' . ltrim($resourceUrl, '/');

            $paymentData = Http::withToken($accessToken)->get($resourceUrl)->json() ?: [];
        }

        $mergedPayload = [
            'webhook' => $payload,
            'payment' => $paymentData,
        ];

        return new PaymentWebhookResult(
            eventId: (string) data_get($payload, 'id', data_get($paymentData, 'id', Str::uuid()->toString())),
            eventType: (string) data_get($payload, 'type', 'mercadopago.payment'),
            status: $this->mapMercadoPagoStatus((string) data_get($paymentData, 'status', 'pending')),
            payload: $mergedPayload,
            depositReference: (string) data_get($paymentData, 'external_reference'),
        );
    }

    public function findDepositFromWebhook(array $payload): ?Deposito
    {
        $reference = (string) data_get($payload, 'payment.external_reference', '');

        if ($reference === '') {
            return null;
        }

        return Deposito::query()
            ->where('gateway_slug', $this->pluginSlug())
            ->where('txid', $reference)
            ->first();
    }

    protected function mapMercadoPagoStatus(string $status): string
    {
        return match (strtolower($status)) {
            'approved', 'accredited' => 'paid',
            'rejected', 'cancelled' => 'failed',
            'refunded', 'charged_back' => 'refunded',
            'expired' => 'expired',
            default => 'pending',
        };
    }
}
