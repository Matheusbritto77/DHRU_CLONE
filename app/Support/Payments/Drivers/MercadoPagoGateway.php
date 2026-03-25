<?php

namespace App\Support\Payments\Drivers;

use App\Models\Deposito;
use App\Models\User;
use App\Support\Payments\AbstractPaymentGateway;
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
