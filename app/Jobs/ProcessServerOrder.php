<?php

namespace App\Jobs;

use App\Domain\Dhru\Services\DhruOrderService;
use App\Models\server_services;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessServerOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected server_services $order)
    {
    }

    public function handle(DhruOrderService $dhruOrderService): void
    {
        try {
            $dhruOrderService->syncServerOrderStatus($this->order->fresh());
        } catch (\Throwable $exception) {
            Log::error('Falha ao sincronizar pedido Server', [
                'referenceid' => $this->order->referenceid,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
