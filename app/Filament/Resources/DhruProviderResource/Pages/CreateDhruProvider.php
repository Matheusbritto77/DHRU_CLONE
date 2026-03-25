<?php

namespace App\Filament\Resources\DhruProviderResource\Pages;

use App\Domain\Dhru\Services\SyncDhruProviderCatalogService;
use App\Filament\Resources\DhruProviderResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDhruProvider extends CreateRecord
{
    protected static string $resource = DhruProviderResource::class;

    protected function afterCreate(): void
    {
        /** @var SyncDhruProviderCatalogService $syncService */
        $syncService = app(SyncDhruProviderCatalogService::class);
        $run = $syncService->sync($this->record);

        Notification::make()
            ->title($run->status === 'success' ? 'Provedor cadastrado e sincronizado' : 'Provedor cadastrado, mas a sincronizacao falhou')
            ->body($run->message ?: 'Verifique o catalogo sincronizado.')
            ->{$run->status === 'success' ? 'success' : 'warning'}()
            ->send();
    }
}
