<?php

namespace App\Support\Payments\Drivers;

use App\Models\Deposito;
use App\Models\User;
use App\Support\Payments\AbstractPaymentGateway;
use App\Support\Payments\PaymentWebhookResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class StripeGateway extends AbstractPaymentGateway
{
    public function pluginSlug(): string
    {
        return 'payment-stripe';
    }

    public function label(): string
    {
        return 'Stripe';
    }

    public function createCheckout(array $payload, User $user): array
    {
        $this->requireEnabled();

        $settings = $this->settings();
        $secretKey = (string) ($settings['secret_key'] ?? '');

        if ($secretKey === '') {
            throw new RuntimeException('Credenciais da Stripe nao configuradas.');
        }

        $reference = (string) Str::uuid();
        $currency = strtolower((string) ($settings['checkout_currency'] ?? 'brl'));

        $response = Http::asForm()
            ->withBasicAuth($secretKey, '')
            ->post('https://api.stripe.com/v1/checkout/sessions', [
                'mode' => 'payment',
                'success_url' => (string) ($settings['success_url'] ?? url('/add-credits')),
                'cancel_url' => (string) ($settings['cancel_url'] ?? url('/add-credits')),
                'payment_method_types[0]' => 'card',
                'metadata[gateway_slug]' => $this->pluginSlug(),
                'metadata[reference]' => $reference,
                'line_items[0][quantity]' => 1,
                'line_items[0][price_data][currency]' => $currency,
                'line_items[0][price_data][unit_amount]' => (int) round(((float) $payload['total_brl']) * 100),
                'line_items[0][price_data][product_data][name]' => 'Recarga de creditos',
            ]);

        $checkoutUrl = $response->json('url');

        if (! $response->successful() || blank($checkoutUrl)) {
            throw new RuntimeException('Falha ao criar checkout Stripe.');
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
        $signature = (string) $request->header('Stripe-Signature');

        if ($secret === '' || $signature === '') {
            return false;
        }

        preg_match('/t=([^,]+)/', $signature, $timestampMatch);
        preg_match('/v1=([^,]+)/', $signature, $hashMatch);

        $timestamp = $timestampMatch[1] ?? null;
        $hash = $hashMatch[1] ?? null;

        if (! $timestamp || ! $hash) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $request->getContent();
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expected, $hash);
    }

    public function handleWebhook(Request $request): PaymentWebhookResult
    {
        $payload = $request->all();
        $object = (array) data_get($payload, 'data.object', []);

        return new PaymentWebhookResult(
            eventId: (string) data_get($payload, 'id', Str::uuid()->toString()),
            eventType: (string) data_get($payload, 'type', 'stripe.event'),
            status: $this->mapStripeStatus((string) data_get($payload, 'type', '')),
            payload: $payload,
            depositReference: (string) data_get($object, 'metadata.reference', data_get($object, 'client_reference_id')),
        );
    }

    public function findDepositFromWebhook(array $payload): ?Deposito
    {
        $reference = (string) data_get($payload, 'data.object.metadata.reference', data_get($payload, 'data.object.client_reference_id', ''));

        if ($reference === '') {
            return null;
        }

        return Deposito::query()
            ->where('gateway_slug', $this->pluginSlug())
            ->where('txid', $reference)
            ->first();
    }

    protected function mapStripeStatus(string $eventType): string
    {
        return match ($eventType) {
            'checkout.session.completed', 'payment_intent.succeeded' => 'paid',
            'checkout.session.expired' => 'expired',
            'charge.refunded' => 'refunded',
            default => 'pending',
        };
    }
}
