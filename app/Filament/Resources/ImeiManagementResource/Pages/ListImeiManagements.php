<?php

namespace App\Filament\Resources\ImeiManagementResource\Pages;

use App\Filament\Resources\ImeiManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImeiManagements extends ListRecords
{
    protected static string $resource = ImeiManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Link directly to the Catalog to add more
            Actions\Action::make('addFromCatalog')
                ->label('Adicionar do Catálogo')
                ->icon('heroicon-o-plus')
                ->url(\App\Filament\Resources\DhruCatalogServiceResource::getUrl() . '?tableFilters[group_type][value]=IMEI'),
        ];
    }
}
