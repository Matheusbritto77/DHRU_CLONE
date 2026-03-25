<?php

namespace App\Contracts\Payments;

use App\Models\Deposito;
use App\Models\User;
use App\Support\Payments\PaymentWebhookResult;
use Illuminate\Http\Request;

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

    public function validateWebhook(Request $request): bool;

    public function handleWebhook(Request $request): PaymentWebhookResult;

    public function findDepositFromWebhook(array $payload): ?Deposito;

    public function confirmDeposit(Deposito $deposit, array $payload, string $status = 'paid'): void;
}
