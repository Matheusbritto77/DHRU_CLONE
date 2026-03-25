<?php

namespace App\Filament\Resources\DepositoResource\Pages;

use App\Filament\Resources\DepositoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepositos extends ListRecords
{
    protected static string $resource = DepositoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
