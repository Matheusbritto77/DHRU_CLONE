<?php

namespace App\Filament\Resources\DhruProviderResource\Pages;

use App\Domain\Dhru\Services\SyncDhruProviderCatalogService;
use App\Filament\Resources\DhruProviderResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDhruProvider extends EditRecord
{
    protected static string $resource = DhruProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('testConnection')
                ->label('Testar conexao')
                ->icon('heroicon-o-signal')
                ->action(function (SyncDhruProviderCatalogService $syncService): void {
                    $run = $syncService->sync($this->record);

                    Notification::make()
                        ->title($run->status === 'success' ? 'Conexao valida' : 'Falha na conexao')
                        ->body($run->message ?: 'Teste concluido.')
                        ->{$run->status === 'success' ? 'success' : 'danger'}()
                        ->send();
                }),
            Actions\Action::make('syncNow')
                ->label('Sincronizar agora')
                ->icon('heroicon-o-arrow-path')
                ->action(function (SyncDhruProviderCatalogService $syncService): void {
                    $run = $syncService->sync($this->record);

                    Notification::make()
                        ->title($run->status === 'success' ? 'Catalogo sincronizado' : 'Falha na sincronizacao')
                        ->body($run->message ?: 'Processo finalizado.')
                        ->{$run->status === 'success' ? 'success' : 'danger'}()
                        ->send();
                }),
            ...parent::getHeaderActions(),
        ];
    }
}
