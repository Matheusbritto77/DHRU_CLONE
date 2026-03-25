<?php

namespace App\Support\Payments\Drivers;

use App\Models\Deposito;
use App\Models\User;
use App\Support\Payments\AbstractPaymentGateway;
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
