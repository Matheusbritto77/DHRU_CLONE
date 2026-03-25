<?php

namespace App\Filament\Shared\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AvailableServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'availableServices';

    protected static ?string $title = 'Serviços Vinculados';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('dhru_catalog_service_id')
                    ->label('Serviço de Origem (Catálogo Sync)')
                    ->relationship('catalogService', 'service_name', function (Builder $query) {
                        return $query->with('provider')->orderBy('service_name');
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->provider->name} - {$record->service_name} (US$ {$record->cost})")
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (!$state) return;
                        $service = \App\Models\DhruCatalogService::find($state);
                        if ($service) {
                            $set('custom_name', $service->service_name);
                            $set('selling_price', $service->cost * 1.2); 
                        }
                    }),
                Forms\Components\TextInput::make('custom_name')
                    ->label('Nome Personalizado')
                    ->placeholder('Deixe vazio para usar o nome original')
                    ->maxLength(255),
                Forms\Components\Textarea::make('custom_description')
                    ->label('Descrição Personalizada')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('selling_price')
                    ->label('Preço de Venda')
                    ->numeric()
                    ->prefix('US$')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Disponível para Clientes')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Ordem')
                    ->numeric()
                    ->default(0)
                    ->unique(
                        table: 'imei_available_services',
                        column: 'sort_order',
                        ignorable: fn ($record) => $record,
                        modifyRuleUsing: fn ($rule, $get) => $rule->where('category_id', $this->getOwnerRecord()->id)
                    )
                    ->validationMessages([
                        'unique' => 'Esta posição na ordem já está sendo usada por outro serviço nesta categoria.',
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['catalogService.provider']))
            ->recordTitleAttribute('custom_name')
            ->columns([
                Tables\Columns\TextColumn::make('catalogService.provider.name')
                    ->label('Origem / Provedor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Nome do Serviço')
                    ->searchable(['custom_name', 'catalogService.service_name']),
                Tables\Columns\TextColumn::make('catalogService.cost')
                    ->label('Custo')
                    ->money('USD'),
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Venda')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('sort_order')
                    ->label('Ordem')
                    ->rules(function ($record) {
                        return [
                            'required',
                            'numeric',
                            'min:1',
                            \Illuminate\Validation\Rule::unique('imei_available_services', 'sort_order')
                                ->where('category_id', $record->category_id)
                                ->ignore($record->id),
                        ];
                    }),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Visível'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['service_type'] = $this->getOwnerRecord()->type;
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
