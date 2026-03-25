<?php

namespace App\Filament\Resources\DhruProviderResource\Pages;

use App\Domain\Dhru\Services\SyncDhruProviderCatalogService;
use App\Filament\Resources\DhruProviderResource;
use App\Models\DhruProvider;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDhruProviders extends ListRecords
{
    protected static string $resource = DhruProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Adicionar provedor'),
            Actions\Action::make('syncActiveProviders')
                ->label('Sincronizar provedores ativos')
                ->icon('heroicon-o-arrow-path')
                ->action(function (SyncDhruProviderCatalogService $syncService): void {
                    $providers = DhruProvider::query()
                        ->where('is_active', true)
                        ->get();

                    if ($providers->isEmpty()) {
                        Notification::make()
                            ->title('Nenhum provedor ativo')
                            ->body('Cadastre ou ative pelo menos um provedor para sincronizar.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $success = 0;
                    $failed = 0;

                    foreach ($providers as $provider) {
                        $run = $syncService->sync($provider);

                        if ($run->status === 'success') {
                            $success++;
                        } else {
                            $failed++;
                        }
                    }

                    Notification::make()
                        ->title('Sincronizacao finalizada')
                        ->body("Sucesso: {$success}. Falhas: {$failed}.")
                        ->{$failed > 0 ? 'warning' : 'success'}()
                        ->send();
                }),
        ];
    }
}
