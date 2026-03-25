<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DhruServiceChangeResource\Pages;
use App\Models\DhruServiceChange;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DhruServiceChangeResource extends Resource
{
    protected static ?string $model = DhruServiceChange::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Dhru';

    protected static ?string $navigationLabel = 'Mudancas';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider.name')->label('Provedor')->searchable(),
                Tables\Columns\TextColumn::make('change_type')->badge(),
                Tables\Columns\TextColumn::make('service.service_name')->label('Servico')->limit(40),
                Tables\Columns\TextColumn::make('service.external_service_id')->label('Service ID'),
                Tables\Columns\TextColumn::make('detected_at')->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dhru_provider_id')
                    ->relationship('provider', 'name')
                    ->label('Provedor'),
                Tables\Filters\SelectFilter::make('change_type')
                    ->options([
                        'added' => 'Adicionado',
                        'updated' => 'Atualizado',
                        'removed' => 'Removido',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('detected_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDhruServiceChanges::route('/'),
            'view' => Pages\ViewDhruServiceChange::route('/{record}'),
        ];
    }
}
