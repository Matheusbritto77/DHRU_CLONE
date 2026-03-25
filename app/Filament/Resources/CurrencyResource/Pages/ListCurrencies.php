<?php

namespace App\Filament\Resources\CurrencyResource\Pages;

use App\Filament\Resources\CurrencyResource;
use App\Services\CurrencyService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('syncCatalog')
                ->label('Importar da API')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->action(function (): void {
                    app(CurrencyService::class)->syncAll();

                    Notification::make()
                        ->title('Moedas e câmbio sincronizados com sucesso')
                        ->success()
                        ->send();
                }),
        ];
    }
}
