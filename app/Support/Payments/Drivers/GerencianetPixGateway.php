<?php

namespace App\Support\Payments\Drivers;

use App\Models\Deposito;
use App\Models\User;
use App\Support\Payments\AbstractPaymentGateway;
use App\Support\Payments\PaymentWebhookResult;
use Gerencianet\Exception\GerencianetException;
use Gerencianet\Gerencianet;
use Illuminate\Http\Request;
use RuntimeException;

class GerencianetPixGateway extends AbstractPaymentGateway
{
    public function pluginSlug(): string
    {
        return 'payment-gerencianet-pix';
    }

    public function label(): string
    {
        return 'Gerencianet PIX';
    }

    public function createCheckout(array $payload, User $user): array
    {
        $this->requireEnabled();

        $settings = $this->settings();
        $options = $this->makeOptions($settings);

        $body = [
            'calendario' => [
                'expiracao' => (int) ($settings['pix_expiration_seconds'] ?? 3600),
            ],
            'valor' => [
                'original' => number_format((float) $payload['total_brl'], 2, '.', ''),
            ],
            'chave' => $settings['pix_key'],
            'solicitacaoPagador' => (string) ($settings['payment_request_message'] ?? 'Pagamento Plataforma'),
        ];

        $api = Gerencianet::getInstance($options);
        $pix = $api->pixCreateImmediateCharge([], $body);

        if (! isset($pix['txid'])) {
            throw new RuntimeException('Falha ao criar cobranca PIX.');
        }

        $deposit = Deposito::create([
            'txid' => $pix['txid'],
            'gateway_slug' => $this->pluginSlug(),
            'gateway_reference' => (string) ($pix['loc']['id'] ?? $pix['txid']),
            'webhook_token' => $this->generateWebhookToken(),
            'gateway_payload' => $pix,
            'valor' => (float) $payload['amount_usd_hidden'],
            'user_id' => $user->id,
            'status' => 0,
            'payment_status_detail' => 'pending',
        ]);

        $qrCode = $api->pixGenerateQRCode([
            'id' => $pix['loc']['id'],
        ]);

        return [
            'type' => isset($qrCode['linkVisualizacao']) ? 'redirect' : 'view',
            'target' => $qrCode['linkVisualizacao'] ?? null,
            'view' => 'qrcode',
            'data' => ['qrcode' => $qrCode['imagemQrcode'] ?? null, 'deposit' => $deposit],
        ];
    }

    public function reconcilePending(): array
    {
        $this->requireEnabled();

        $settings = $this->settings();
        $minAge = (int) ($settings['reconcile_min_age_minutes'] ?? 5);
        $maxAge = (int) ($settings['reconcile_max_age_minutes'] ?? 30);

        $deposits = Deposito::query()
            ->where('gateway_slug', $this->pluginSlug())
            ->where('status', 0)
            ->whereBetween('created_at', [now()->subMinutes($maxAge), now()->subMinutes($minAge)])
            ->get();

        if ($deposits->isEmpty()) {
            return ['checked' => 0, 'updated' => 0];
        }

        $api = Gerencianet::getInstance($this->makeOptions($settings));
        $pixReceived = $api->pixReceivedList([
            'inicio' => now()->subMinutes($maxAge)->toIso8601ZuluString(),
            'fim' => now()->toIso8601ZuluString(),
        ]);

        $receivedByTxid = collect($pixReceived['pix'] ?? [])->keyBy('txid');
        $updated = 0;

        foreach ($deposits as $deposit) {
            if (! $receivedByTxid->has($deposit->txid)) {
                continue;
            }

            $this->confirmDeposit($deposit, (array) $receivedByTxid->get($deposit->txid), 'paid');
            $updated++;
        }

        return ['checked' => $deposits->count(), 'updated' => $updated];
    }

    public function validateWebhook(Request $request): bool
    {
        $secret = (string) ($this->settings()['reconcile_webhook_secret'] ?? '');
        $provided = $request->header('X-Gateway-Webhook-Secret', $request->string('secret')->toString());

        return $secret !== '' && hash_equals($secret, $provided);
    }

    public function handleWebhook(Request $request): PaymentWebhookResult
    {
        $payload = $request->all();
        $txid = (string) data_get($payload, 'pix.0.txid', data_get($payload, 'txid', ''));

        return new PaymentWebhookResult(
            eventId: (string) data_get($payload, 'id', $txid ?: $this->generateWebhookToken()),
            eventType: (string) data_get($payload, 'evento', 'pix.received'),
            status: 'paid',
            payload: $payload,
            depositReference: $txid !== '' ? $txid : null,
        );
    }

    public function findDepositFromWebhook(array $payload): ?Deposito
    {
        $txid = (string) data_get($payload, 'pix.0.txid', data_get($payload, 'txid', ''));

        if ($txid === '') {
            return null;
        }

        return Deposito::query()
            ->where('gateway_slug', $this->pluginSlug())
            ->where('txid', $txid)
            ->first();
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    protected function makeOptions(array $settings): array
    {
        $mode = ($settings['mode'] ?? 'production') === 'sandbox' ? 'sandbox' : 'production';
        $certificate = (string) ($settings["{$mode}_certificate_name"] ?? '');

        return [
            'client_id' => $settings["{$mode}_client_id"] ?? null,
            'client_secret' => $settings["{$mode}_client_secret"] ?? null,
            'certificate' => base_path("certs/{$certificate}"),
            'sandbox' => $mode === 'sandbox',
            'debug' => (bool) ($settings['debug'] ?? false),
            'timeout' => 30,
        ];
    }
}
