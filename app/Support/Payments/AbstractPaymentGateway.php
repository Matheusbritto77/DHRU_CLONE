<?php

namespace App\Support\Payments;

use App\Contracts\Payments\PaymentGatewayDriver;
use App\Models\Deposito;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

abstract class AbstractPaymentGateway implements PaymentGatewayDriver
{
    public function isEnabled(): bool
    {
        return (bool) ($this->settings()['enabled'] ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return app(PaymentGatewayRegistry::class)->settings($this->pluginSlug());
    }

    protected function requireEnabled(): void
    {
        if (! $this->isEnabled()) {
            throw new RuntimeException("Gateway [{$this->label()}] desabilitado.");
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createCheckout(array $payload, User $user): array
    {
        throw new RuntimeException("Checkout nao implementado para [{$this->label()}].");
    }

    /**
     * @return array<string, mixed>
     */
    public function reconcilePending(): array
    {
        return ['checked' => 0, 'updated' => 0];
    }

    public function validateWebhook(Request $request): bool
    {
        return false;
    }

    public function handleWebhook(Request $request): PaymentWebhookResult
    {
        throw new RuntimeException("Webhook nao implementado para [{$this->label()}].");
    }

    public function findDepositFromWebhook(array $payload): ?Deposito
    {
        return null;
    }

    public function confirmDeposit(Deposito $deposit, array $payload, string $status = 'paid'): void
    {
        DB::transaction(function () use ($deposit, $payload, $status): void {
            $deposit->refresh();

            if ($deposit->credited_at) {
                $deposit->update([
                    'payment_status_detail' => $status,
                    'webhook_last_payload' => $payload,
                ]);

                return;
            }

            $deposit->update([
                'status' => 1,
                'paid_at' => $deposit->paid_at ?? now(),
                'credited_at' => now(),
                'credited_amount' => $deposit->valor,
                'payment_status_detail' => $status,
                'webhook_last_payload' => $payload,
            ]);

            $user = User::query()->find($deposit->user_id);

            if ($user) {
                $user->increment('credit', (float) $deposit->valor);
            }
        });
    }

    protected function generateWebhookToken(): string
    {
        return Str::uuid()->toString();
    }
}
