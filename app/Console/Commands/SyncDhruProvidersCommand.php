<?php

namespace App\Console\Commands;

use App\Domain\Dhru\Services\SyncDhruProviderCatalogService;
use App\Models\DhruProvider;
use Illuminate\Console\Command;

class SyncDhruProvidersCommand extends Command
{
    protected $signature = 'dhru:sync-providers {provider_id?}';

    protected $description = 'Sincroniza o catalogo dos provedores Dhru registrados.';

    public function handle(SyncDhruProviderCatalogService $syncService): int
    {
        $providerId = $this->argument('provider_id');

        $providers = DhruProvider::query()
            ->when($providerId, fn ($query) => $query->whereKey($providerId))
            ->where('is_active', true)
            ->get();

        if ($providers->isEmpty()) {
            $this->warn('Nenhum provedor Dhru ativo encontrado.');

            return self::SUCCESS;
        }

        foreach ($providers as $provider) {
            $this->info("Sincronizando {$provider->name}...");
            $run = $syncService->sync($provider);
            $this->line("Status: {$run->status} | vistos={$run->services_seen} novos={$run->services_created} atualizados={$run->services_updated} removidos={$run->services_removed}");
        }

        return self::SUCCESS;
    }
}
