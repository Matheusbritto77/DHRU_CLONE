<?php

namespace App\Contracts\Payments;

use App\Models\User;

interface PaymentGatewayDriver
{
    public function pluginSlug(): string;

    public function label(): string;

    public function isEnabled(): bool;

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createCheckout(array $payload, User $user): array;

    /**
     * @return array<string, mixed>
     */
    public function reconcilePending(): array;
}
