<?php

namespace App\Domain\Dhru\Services;

use App\Models\DhruCatalogService;

class DhruCatalogListingService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getServicesForType(string $type): array
    {
        $normalizedType = strtoupper($type);
        \Illuminate\Support\Facades\Log::info("Listing services for type: {$normalizedType}");

        // Fetch services through the new category-centric vitrine system
        $query = \App\Models\ImeiAvailableService::query()
            ->with(['category', 'catalogService.provider'])
            ->where('imei_available_services.service_type', $normalizedType)
            ->where('imei_available_services.is_active', true)
            ->whereHas('category', function($q) use ($normalizedType) {
                $q->where('type', $normalizedType)->where('is_active', true);
            })
            ->leftJoin('imei_service_categories', 'imei_available_services.category_id', '=', 'imei_service_categories.id')
            ->select('imei_available_services.*')
            ->orderBy('imei_service_categories.sort_order')
            ->orderBy('imei_available_services.sort_order');

        \Illuminate\Support\Facades\Log::info("SQL Query for {$normalizedType}: " . $query->toSql());
        \Illuminate\Support\Facades\Log::info("Bindings for {$normalizedType}: " . json_encode($query->getBindings()));

        $services = $query->get();

        \Illuminate\Support\Facades\Log::info("Found " . $services->count() . " services for type: {$normalizedType}");

        return $services->map(function (\App\Models\ImeiAvailableService $availableService) use ($normalizedType): array {
                $catalog = $availableService->catalogService;

                if (!$catalog) {
                    \Illuminate\Support\Facades\Log::warning("Service ID {$availableService->id} missing catalog service!");
                    return [];
                }

                return [
                    'GROUPNAME' => $availableService->category->name ?? 'Sem Categoria',
                    'SERVICENAME' => $availableService->custom_name ?: $catalog->service_name,
                    'TIME' => $catalog->time_text,
                    'CREDIT' => (float) $availableService->selling_price,
                    'MINQNT' => $catalog->min_qnt ?: '',
                    'MAXQNT' => $catalog->max_qnt ?: '',
                    'SERVICEID' => (string) $catalog->external_service_id,
                    'CATALOG_SERVICE_ID' => $catalog->id,
                    'PROVIDER_ID' => $catalog->dhru_provider_id,
                    'PROVIDER_NAME' => $catalog->provider?->name,
                    'customname' => $availableService->custom_name ?? '',
                    'fieldname' => implode(', ', $catalog->custom_fields ?? []),
                    'description' => $availableService->custom_description ?: '',
                ];
            })
            ->filter()
            ->all();
    }
}
