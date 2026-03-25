<?php

namespace App\Domain\Dhru\Services;

use App\Domain\Dhru\Contracts\DhruProviderClientInterface;
use App\Models\DhruCatalogService;
use App\Models\DhruProvider;
use App\Models\DhruServiceChange;
use App\Models\DhruSyncRun;
use App\Models\ImeiAvailableService;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SyncDhruProviderCatalogService
{
    public function __construct(
        protected DhruProviderClientInterface $client,
    ) {
    }

    public function sync(DhruProvider $provider): DhruSyncRun
    {
        $run = $provider->syncRuns()->create([
            'status' => 'running',
            'started_at' => now(),
        ]);

        try {
            $payload = $this->client->fetchServiceCatalog($provider);
            $normalizedServices = $this->normalizeCatalog($payload);

            $stats = DB::transaction(function () use ($provider, $normalizedServices): array {
                return $this->persistCatalog($provider, $normalizedServices);
            });

            $run->update([
                'status' => 'success',
                'services_seen' => $stats['seen'],
                'services_created' => $stats['created'],
                'services_updated' => $stats['updated'],
                'services_removed' => $stats['removed'],
                'finished_at' => now(),
                'message' => 'Sincronizacao concluida com sucesso.',
            ]);

            $provider->update([
                'last_synced_at' => now(),
                'last_sync_status' => 'success',
                'last_sync_message' => 'Catalogo sincronizado com sucesso.',
            ]);
        } catch (Throwable $exception) {
            $run->update([
                'status' => 'failed',
                'finished_at' => now(),
                'message' => $exception->getMessage(),
            ]);

            $provider->update([
                'last_sync_status' => 'failed',
                'last_sync_message' => $exception->getMessage(),
            ]);
        }

        return $run->fresh();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function normalizeCatalog(array $payload): array
    {
        return collect($payload['SUCCESS'] ?? [])
            ->flatMap(function (array $successItem) {
                return collect($successItem['LIST'] ?? [])
                    ->flatMap(function (array $group) {
                        $groupName = Arr::get($group, 'GROUPNAME');
                        $groupType = strtoupper((string) Arr::get($group, 'GROUPTYPE', 'UNKNOWN'));

                        return collect($group['SERVICES'] ?? [])
                            ->map(function (array $service) use ($groupName, $groupType) {
                                $customFields = collect($service['Requires.Custom'] ?? [])
                                    ->pluck('fieldname')
                                    ->filter()
                                    ->values()
                                    ->all();

                                $normalized = [
                                    'group_name' => $groupName,
                                    'group_type' => $groupType,
                                    'service_type' => strtoupper((string) Arr::get($service, 'SERVICETYPE', $groupType)),
                                    'external_service_id' => (int) Arr::get($service, 'SERVICEID'),
                                    'service_name' => (string) Arr::get($service, 'SERVICENAME'),
                                    'time_text' => Arr::get($service, 'TIME'),
                                    'cost' => (float) Arr::get($service, 'CREDIT', 0),
                                    'min_qnt' => (int) Arr::get($service, 'MINQNT', 0),
                                    'max_qnt' => (int) Arr::get($service, 'MAXQNT', 0),
                                    'custom_name' => Arr::get($service, 'CUSTOM.customname'),
                                    'custom_fields' => $customFields,
                                    'raw_payload' => $service,
                                ];

                                $normalized['fingerprint'] = sha1(json_encode([
                                    $normalized['group_name'],
                                    $normalized['group_type'],
                                    $normalized['service_type'],
                                    $normalized['service_name'],
                                    $normalized['time_text'],
                                    $normalized['cost'],
                                    $normalized['min_qnt'],
                                    $normalized['max_qnt'],
                                    $normalized['custom_name'],
                                    $normalized['custom_fields'],
                                ], JSON_UNESCAPED_UNICODE));

                                return $normalized;
                            });
                    });
            })
            ->filter(fn (array $service) => $service['external_service_id'] > 0 && filled($service['service_name']))
            ->values()
            ->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $normalizedServices
     * @return array{seen:int,created:int,updated:int,removed:int}
     */
    protected function persistCatalog(DhruProvider $provider, array $normalizedServices): array
    {
        $existing = $provider->services()->get()->keyBy('external_service_id');
        $seenIds = [];
        $created = 0;
        $updated = 0;
        $removed = 0;
        $now = now();

        foreach ($normalizedServices as $serviceData) {
            $serviceId = $serviceData['external_service_id'];
            $seenIds[] = $serviceId;

            /** @var DhruCatalogService|null $record */
            $record = $existing->get($serviceId);

            if (! $record) {
                $record = $provider->services()->create(array_merge($serviceData, [
                    'is_active' => true,
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                ]));

                $this->recordChange($provider, $record, 'added', [], null, $record->toArray());
                $created++;
                continue;
            }

            $before = $record->toArray();
            $changedFields = $this->diffFields($record, $serviceData);

            $record->fill(array_merge($serviceData, [
                'is_active' => true,
                'last_seen_at' => $now,
            ]));

            if ($record->isDirty()) {
                $record->save();
                $this->recordChange($provider, $record, 'updated', $changedFields, $before, $record->fresh()->toArray());
                $this->propagateChanges($record, $changedFields);
                $updated++;
            } else {
                $record->forceFill([
                    'is_active' => true,
                    'last_seen_at' => $now,
                ])->save();
            }
        }

        $provider->services()
            ->whereNotIn('external_service_id', $seenIds)
            ->where('is_active', true)
            ->chunkById(200, function (\Illuminate\Support\Collection $services) use ($provider, &$removed) {
                foreach ($services as $service) {
                    /** @var DhruCatalogService $service */
                    $before = $service->toArray();
                    $service->update([
                        'is_active' => false,
                    ]);

                    $this->recordChange($provider, $service, 'removed', ['is_active' => [true, false]], $before, $service->fresh()->toArray());
                    $this->propagateChanges($service, ['is_active' => [true, false]]);
                    $removed++;
                }
            });

        return [
            'seen' => count($normalizedServices),
            'created' => $created,
            'updated' => $updated,
            'removed' => $removed,
        ];
    }

    protected function diffFields(DhruCatalogService $record, array $incoming): array
    {
        $fields = [
            'group_name',
            'group_type',
            'service_type',
            'service_name',
            'time_text',
            'cost',
            'min_qnt',
            'max_qnt',
            'custom_name',
            'custom_fields',
            'fingerprint',
        ];

        $changes = [];

        foreach ($fields as $field) {
            $current = $record->getAttribute($field);
            $next = $incoming[$field] ?? null;

            if ($current != $next) {
                $changes[$field] = [$current, $next];
            }
        }

        return $changes;
    }

    protected function recordChange(
        DhruProvider $provider,
        DhruCatalogService $service,
        string $type,
        array $changedFields,
        ?array $before,
        ?array $after
    ): void {
        DhruServiceChange::create([
            'dhru_provider_id' => $provider->id,
            'dhru_catalog_service_id' => $service->id,
            'change_type' => $type,
            'changed_fields' => $changedFields,
            'snapshot_before' => $before,
            'snapshot_after' => $after,
            'detected_at' => Carbon::now(),
        ]);
    }

    protected function propagateChanges(DhruCatalogService $service, array $changes): void
    {
        // Se o serviço foi desativado no provedor, desativamos na nossa lista customizada também para evitar erros de pedido
        if (isset($changes['is_active']) && $changes['is_active'][1] === false) {
            $service->customServices()->update(['is_active' => false]);
        }

        // Se o custo mudou, poderíamos alertar ou ajustar margens. 
        // Por segurança, apenas marcamos que precisa de revisão se o custo subir acima do preço de venda
        if (isset($changes['cost'])) {
            $newCost = $changes['cost'][1];
            $service->customServices()
                ->where('selling_price', '<', $newCost)
                ->update(['is_active' => false]); // Desativa se o custo ficou maior que a venda para não ter prejuízo
        }
    }
}
