<?php

namespace App\Jobs;

use App\Domain\Dhru\Services\DhruOrderService;
use App\Models\ImeiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImeiOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ImeiService $imeiOrder)
    {
    }

    public function handle(DhruOrderService $dhruOrderService): void
    {
        try {
            $dhruOrderService->syncImeiOrderStatus($this->imeiOrder->fresh());
        } catch (\Throwable $exception) {
            Log::error('Falha ao sincronizar pedido IMEI', [
                'referenceid' => $this->imeiOrder->referenceid,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
