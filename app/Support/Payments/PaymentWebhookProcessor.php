<?php

namespace App\Support\Payments;

use App\Contracts\Payments\PaymentGatewayDriver;
use App\Models\Deposito;
use App\Models\PaymentEvent;
use Illuminate\Support\Facades\DB;

class PaymentWebhookProcessor
{
    /**
     * @return array<string, mixed>
     */
    public function process(string $gatewaySlug, PaymentGatewayDriver $driver, PaymentWebhookResult $result): array
    {
        $existingEvent = PaymentEvent::query()
            ->where('gateway_slug', $gatewaySlug)
            ->where('event_id', $result->eventId)
            ->first();

        if ($existingEvent?->processed_at) {
            return [
                'ok' => true,
                'duplicate' => true,
                'event_id' => $existingEvent->event_id,
                'deposit_id' => $existingEvent->deposito_id,
            ];
        }

        return DB::transaction(function () use ($gatewaySlug, $driver, $result, $existingEvent): array {
            $event = $existingEvent ?? PaymentEvent::query()->create([
                'gateway_slug' => $gatewaySlug,
                'event_id' => $result->eventId,
                'event_type' => $result->eventType,
                'status' => 'received',
                'payload' => $result->payload,
            ]);

            $deposit = $driver->findDepositFromWebhook($result->payload);

            if (! $deposit) {
                $event->update([
                    'status' => 'ignored',
                    'processed_at' => now(),
                    'payload' => $result->payload,
                ]);

                return [
                    'ok' => true,
                    'duplicate' => false,
                    'event_id' => $event->event_id,
                    'deposit_id' => null,
                    'status' => 'ignored',
                ];
            }

            if ($result->status === 'paid') {
                $driver->confirmDeposit($deposit, $result->payload, $result->status);
            } else {
                $deposit->update([
                    'payment_status_detail' => $result->status,
                    'webhook_last_payload' => $result->payload,
                ]);
            }

            $event->update([
                'status' => 'processed',
                'deposito_id' => $deposit->id,
                'processed_at' => now(),
                'payload' => $result->payload,
            ]);

            return [
                'ok' => true,
                'duplicate' => false,
                'event_id' => $event->event_id,
                'deposit_id' => $deposit->id,
                'status' => 'processed',
            ];
        });
    }
}
