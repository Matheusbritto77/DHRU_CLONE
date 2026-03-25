<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Services\CurrencyService;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $navigationLabel = 'Moedas e Câmbio';
    protected static ?string $modelLabel = 'Moeda';
    protected static ?string $pluralModelLabel = 'Moedas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Moeda')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('code')->label('Código (ISO 4217)')->placeholder('USD, BRL, EUR')->required()->unique(ignoreRecord: true)->maxLength(3),
                            Forms\Components\TextInput::make('symbol')->label('Símbolo')->placeholder('$, R€, €')->required()->maxLength(10),
                        ]),
                        Forms\Components\TextInput::make('name')->label('Nome')->placeholder('Ex: Dólar Americano')->required(),
                    ]),
                
                Forms\Components\Section::make('Configurações de Câmbio')
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('exchange_rate')->label('Taxa de Câmbio')->numeric()->required()->default(1.0)->helperText('Quanto desta moeda vale 1 unidade da moeda base.'),
                            Forms\Components\Toggle::make('is_base')->label('Moeda Base (USD)')->default(false)->helperText('Apenas uma moeda deve ser a base. Os preços dos serviços são registrados nela.'),
                            Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Cód')->badge()->color('info')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable(),
                Tables\Columns\TextColumn::make('symbol')->label('Símbolo'),
                Tables\Columns\TextColumn::make('exchange_rate')->label('Taxa (vs Base)')->numeric(4)->sortable(),
                Tables\Columns\IconColumn::make('is_base')->label('Base')->boolean()->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('updateRate')
                    ->label('Atualizar Taxa (API)')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (Currency $record) {
                        (new CurrencyService())->syncAll();
                        Notification::make()->title("Taxas de câmbio atualizadas!")->success()->send();
                    })
                    ->hidden(fn (Currency $record) => $record->is_base),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
