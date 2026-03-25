<?php

namespace App\Support\Payments;

use App\Contracts\Payments\PaymentGatewayDriver;
use App\Models\User;
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
}
