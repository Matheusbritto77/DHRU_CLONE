<?php

namespace App\Filament\Resources\ServerManagementResource\Pages;

use App\Filament\Resources\ServerManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServerManagements extends ListRecords
{
    protected static string $resource = ServerManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('addFromCatalog')
                ->label('Adicionar do Catálogo')
                ->icon('heroicon-o-plus')
                ->url(\App\Filament\Resources\DhruCatalogServiceResource::getUrl() . '?tableFilters[group_type][value]=SERVER'),
        ];
    }
}
