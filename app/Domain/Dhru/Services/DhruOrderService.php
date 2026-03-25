<?php

namespace App\Domain\Dhru\Services;

use App\Domain\Dhru\Contracts\DhruProviderClientInterface;
use App\Models\DhruCatalogService;
use App\Models\DhruProvider;
use App\Models\ImeiService;
use App\Models\server_services;
use RuntimeException;

class DhruOrderService
{
    public function __construct(
        protected DhruProviderClientInterface $client,
    ) {
    }

    public function placeImeiOrder(DhruCatalogService $catalogService, string $serialNumber): array
    {
        return $this->client->request($catalogService->provider, 'placeimeiorder', [
            'IMEI' => $serialNumber,
            'ID' => $catalogService->external_service_id,
            'CUSTOMFIELD' => base64_encode(json_encode([
                'SERIAL_NUMBER' => $serialNumber,
                'SN' => $serialNumber,
            ])),
        ]);
    }

    public function placeServerOrder(DhruCatalogService $catalogService, string $serializedCustomFields, ?int $quantity = null): array
    {
        $customFields = $this->parseLegacySerializedFields($serializedCustomFields);

        return $this->client->request($catalogService->provider, 'placeimeiorder', [
            'IMEI' => '',
            'Qnt' => $quantity ?: 0,
            'ID' => $catalogService->external_service_id,
            'CUSTOMFIELD' => base64_encode(json_encode($customFields)),
        ]);
    }

    public function fetchBulkStatus(DhruProvider $provider, string $referenceId): array
    {
        return $this->client->request($provider, 'getimeiorderbulk', base64_encode(json_encode([
            ['ID' => $referenceId],
        ])), true);
    }

    public function syncImeiOrderStatus(ImeiService $order): void
    {
        if (! $order->dhruProvider) {
            throw new RuntimeException('Pedido IMEI sem provedor vinculado.');
        }

        $this->syncOrderStatus($order, $this->fetchBulkStatus($order->dhruProvider, $order->referenceid));
    }

    public function syncServerOrderStatus(server_services $order): void
    {
        if (! $order->dhruProvider) {
            throw new RuntimeException('Pedido Server sem provedor vinculado.');
        }

        $this->syncOrderStatus($order, $this->fetchBulkStatus($order->dhruProvider, $order->referenceid));
    }

    protected function syncOrderStatus(ImeiService|server_services $order, array $response): void
    {
        foreach ($this->extractSuccessItems($response) as $item) {
            $status = (int) ($item['STATUS'] ?? $order->status);
            $code = $item['CODE'] ?? $order->code;

            if ((int) $order->status === $status) {
                continue;
            }

            if ($status === 3 && stripos((string) $order->servicename, 'No Refund') === false) {
                $this->refundUser($order->user, (float) $order->cost);
            }

            $order->update([
                'status' => $status,
                'code' => $code,
            ]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractSuccessItems(array $response): array
    {
        foreach ($response as $value) {
            if (is_array($value) && isset($value['SUCCESS']) && is_array($value['SUCCESS'])) {
                return $value['SUCCESS'];
            }
        }

        return [];
    }

    /**
     * @return array<string, string>
     */
    protected function parseLegacySerializedFields(string $serializedFields): array
    {
        $serializedFields = trim($serializedFields, '"');
        $parts = $serializedFields === '' ? [] : explode('","', $serializedFields);
        $mapped = [];

        foreach ($parts as $part) {
            $keyValue = explode('":"', $part, 2);

            if (count($keyValue) === 2) {
                $mapped[$keyValue[0]] = $keyValue[1];
            }
        }

        return $mapped;
    }

    protected function refundUser($user, float $amount): void
    {
        if (! $user) {
            return;
        }

        $user->credit += $amount;
        $user->save();
    }
}
