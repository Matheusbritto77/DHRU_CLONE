<?php

namespace App\Services;

use App\Models\DhruCatalogService;
use App\Models\ImeiAvailableService;
use Illuminate\Support\Collection;

/**
 * Service to manage catalog items adding to the storefront (vitrine).
 */
class VitrineService
{
    /**
     * Adds a single service to the vitrine.
     */
    public function addService(DhruCatalogService $catalogService, int $categoryId, float $price, ?string $customName = null): bool
    {
        if ($this->isAlreadyInVitrine($catalogService)) {
            return false;
        }

        // Calcula proxima ordem na categoria
        $nextOrder = ImeiAvailableService::where('category_id', $categoryId)->max('sort_order') + 1;

        ImeiAvailableService::create([
            'category_id' => $categoryId,
            'dhru_catalog_service_id' => $catalogService->id,
            'custom_name' => $customName,
            'selling_price' => $price,
            'is_active' => true,
            'sort_order' => $nextOrder,
            'service_type' => $catalogService->group_type, // Sync type
        ]);

        // Specific table sync if requested (imei_services or server_services)
        if ($catalogService->group_type === 'IMEI') {
            \App\Models\ImeiService::updateOrCreate(
                ['dhru_catalog_service_id' => $catalogService->id, 'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1],
                [
                    'servicename' => $customName ?? $catalogService->service_name,
                    'serviceid' => (int) $catalogService->external_service_id,
                    'dhru_provider_id' => $catalogService->dhru_provider_id,
                    'cost' => $price,
                    'referenceid' => 'VITRINE',
                    'status' => 1,
                    'IMEI' => '',
                ]
            );
        } elseif ($catalogService->group_type === 'SERVER') {
            \App\Models\server_services::updateOrCreate(
                ['dhru_catalog_service_id' => $catalogService->id, 'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1],
                [
                    'servicename' => $customName ?? $catalogService->service_name,
                    'serviceid' => (int) $catalogService->external_service_id,
                    'dhru_provider_id' => $catalogService->dhru_provider_id,
                    'cost' => $price,
                    'referenceid' => 'VITRINE',
                    'status' => 1,
                    'IMEI' => '',
                ]
            );
        }

        return true;
    }

    /**
     * Adds multiple services with custom data.
     */
    public function addBatchWithCustomData(array $servicesData, int $categoryId): int
    {
        $count = 0;
        foreach ($servicesData as $data) {
            $service = DhruCatalogService::find($data['id']);
            if ($service && $this->addService($service, $categoryId, (float) $data['final_price'], $data['service_name'] ?? null)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Adds multiple services (Collection of DhruCatalogService) to the vitrine.
     */
    public function addBatch(Collection $services, int $categoryId, float $markup): int
    {
        $count = 0;
        foreach ($services as $service) {
            $price = $service->cost * (1 + ($markup / 100));
            if ($this->addService($service, $categoryId, $price)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Checks if a catalog service is already in the vitrine.
     */
    public function isAlreadyInVitrine(DhruCatalogService $service): bool
    {
        return ImeiAvailableService::where('dhru_catalog_service_id', $service->id)->exists();
    }
}
