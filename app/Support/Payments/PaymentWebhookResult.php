<?php

namespace App\Support\Payments;

class PaymentWebhookResult
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly string $eventId,
        public readonly string $eventType,
        public readonly string $status,
        public readonly array $payload,
        public readonly ?string $depositReference = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_type' => $this->eventType,
            'status' => $this->status,
            'deposit_reference' => $this->depositReference,
            'payload' => $this->payload,
        ];
    }
}
