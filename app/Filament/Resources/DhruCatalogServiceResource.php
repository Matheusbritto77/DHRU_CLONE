<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DhruCatalogServiceResource\Pages;
use App\Models\DhruCatalogService;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DhruCatalogServiceResource extends Resource
{
    protected static ?string $model = DhruCatalogService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Dhru';

    protected static ?string $navigationLabel = 'Catalogo';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->with(['provider'])
                ->select([
                    'id',
                    'dhru_provider_id',
                    'group_type',
                    'group_name',
                    'service_name',
                    'cost',
                    'time_text',
                    'is_active',
                    'updated_at'
                ])
                ->withExists('customServices as in_custom_list_flag')
            )
            ->columns([
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provedor')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('group_type')
                    ->badge()
                    ->color(fn ($state) => $state === 'SERVER' ? 'warning' : 'info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('group_name')
                    ->label('Grupo')
                    ->searchable()
                    ->limit(30)
                    ->color('gray')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('service_name')
                    ->label('Serviço')
                    ->searchable()
                    ->limit(45)
                    ->weight('semibold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('cost')
                    ->label('Custo')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time_text')
                    ->label('Tempo')
                    ->limit(20)
                    ->color('gray')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('in_custom_list_flag')
                    ->label('Status Vitrine')
                    ->boolean()
                    ->trueIcon('heroicon-o-shopping-bag')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn ($state) => $state ? 'Já está na vitrine' : 'Não adicionado'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('dhru_provider_id')
                    ->relationship('provider', 'name')
                    ->label('Provedor'),
                Tables\Filters\SelectFilter::make('group_type')
                    ->options([
                        'IMEI' => 'IMEI',
                        'SERVER' => 'SERVER',
                    ]),
                Tables\Filters\SelectFilter::make('group_name')
                    ->options(fn () => \App\Models\DhruCatalogService::query()
                        ->distinct()
                        ->orderBy('group_name')
                        ->pluck('group_name', 'group_name')
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->label('Nome do Grupo'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Ativo'),
                Tables\Filters\TernaryFilter::make('in_custom_list')
                    ->label('Na Vitrine')
                    ->queries(
                        true: fn ($query) => $query->whereHas('customServices'),
                        false: fn ($query) => $query->whereDoesntHave('customServices'),
                    ),
            ])
            ->groups([
                Tables\Grouping\Group::make('group_name')
                    ->label('Nome do Grupo')
                    ->collapsible(),
                Tables\Grouping\Group::make('provider.name')
                    ->label('Provedor')
                    ->collapsible(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('batchImport')
                    ->label('Importação em Lote Expert')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->color('primary')
                    ->form([
                        Select::make('scope')
                            ->label('Escopo da Importação')
                            ->options([
                                'all' => 'Tudo (Todos os Provedores)',
                                'provider' => 'Por Provedor Específico',
                                'group' => 'Por Grupo Específico',
                            ])
                            ->live()
                            ->required(),
                        Select::make('provider_id')
                            ->label('Provedor')
                            ->relationship('provider', 'name')
                            ->hidden(fn ($get) => $get('scope') !== 'provider')
                            ->required(fn ($get) => $get('scope') === 'provider'),
                        Select::make('group_name')
                            ->label('Nome do Grupo')
                            ->options(fn () => \App\Models\DhruCatalogService::distinct()->pluck('group_name', 'group_name'))
                            ->searchable()
                            ->hidden(fn ($get) => $get('scope') !== 'group')
                            ->required(fn ($get) => $get('scope') === 'group'),
                        Select::make('category_id')
                            ->label('Categoria de Destino na Vitrine')
                            ->options(fn ($get) => \App\Models\ImeiServiceCategory::where('type', strtoupper($get('scope') === 'group' ? \App\Models\DhruCatalogService::where('group_name', $get('group_name'))->first()?->group_type ?? 'IMEI' : 'IMEI'))->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        \Filament\Forms\Components\Toggle::make('use_custom_markup')
                            ->label('Aplicar margem de lucro?')
                            ->default(true)
                            ->reactive(),
                        TextInput::make('markup')
                            ->label('Margem de Lucro (%)')
                            ->numeric()
                            ->default(20)
                            ->suffix('%')
                            ->visible(fn ($get) => $get('use_custom_markup')),
                    ])
                    ->action(function (array $data, \App\Services\VitrineService $service) {
                        $query = \App\Models\DhruCatalogService::query()->where('is_active', true);
                        
                        if ($data['scope'] === 'provider') {
                            $query->where('dhru_provider_id', $data['provider_id']);
                        } elseif ($data['scope'] === 'group') {
                            $query->where('group_name', $data['group_name']);
                        }

                        $records = $query->get();
                        $markup = ($data['use_custom_markup'] ?? false) ? (float) ($data['markup'] ?? 0) : 0;
                        $count = $service->addBatch($records, $data['category_id'], $markup);

                        \Filament\Notifications\Notification::make()
                            ->title('Importação Concluída')
                            ->body("Foi(ram) adicionado(s) {$count} novo(s) serviço(s) à vitrine.")
                            ->success()
                            ->send();
                    })
                    ->modalIcon('heroicon-o-bolt')
                    ->modalDescription('Use esta ferramenta para popular sua vitrine de forma acelerada.')
                    ->modalSubmitActionLabel('Iniciar Processo'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('addToCustom')
                        ->label('Add à Vitrine')
                        ->icon('heroicon-o-plus-circle')
                        ->color('success')
                        ->hidden(fn ($record) => $record->customServices()->exists())
                        ->form([
                            \Filament\Forms\Components\Checkbox::make('create_category_from_group')
                                ->label('Criar categoria automática com o nome do grupo?')
                                ->default(true)
                                ->reactive(),
                            Select::make('category_id')
                                ->label('Categoria')
                                ->options(fn ($record) => \App\Models\ImeiServiceCategory::where('type', $record->group_type)->pluck('name', 'id'))
                                ->hidden(fn ($get) => $get('create_category_from_group'))
                                ->required(fn ($get) => !$get('create_category_from_group')),
                            \Filament\Forms\Components\Toggle::make('use_custom_markup')
                                ->label('Aplicar margem?')
                                ->default(true)
                                ->reactive(),
                            TextInput::make('markup')
                                ->label('Margem (%)')
                                ->numeric()
                                ->default(20)
                                ->suffix('%')
                                ->visible(fn ($get) => $get('use_custom_markup')),
                        ])
                        ->action(function (DhruCatalogService $record, array $data, \App\Services\VitrineService $service) {
                            $categoryId = $data['category_id'] ?? null;
                            
                            if ($data['create_category_from_group'] ?? false) {
                                $nextCatOrder = \App\Models\ImeiServiceCategory::max('sort_order') + 1;
                                $category = \App\Models\ImeiServiceCategory::firstOrCreate(
                                    ['name' => $record->group_name],
                                    ['is_active' => true, 'type' => $record->group_type, 'sort_order' => $nextCatOrder]
                                );
                                $categoryId = $category->id;
                            }

                            $markup = ($data['use_custom_markup'] ?? false) ? (float) ($data['markup'] ?? 0) : 0;
                            $price = $record->cost * (1 + ($markup / 100));
                            if ($service->addService($record, $categoryId, $price)) {
                                \Filament\Notifications\Notification::make()->title('Adicionado com sucesso')->success()->send();
                            } else {
                                \Filament\Notifications\Notification::make()->title('Já está na vitrine')->warning()->send();
                            }
                        }),
                    Tables\Actions\Action::make('addGroupToCustom')
                        ->label('Add Grupo Inteiro')
                        ->icon('heroicon-o-rectangle-stack')
                        ->color('info')
                        ->modalHeading(fn ($record) => "Configurar Grupo: {$record->group_name}")
                        ->modalWidth('5xl')
                        ->form([
                            \Filament\Forms\Components\Checkbox::make('create_category_from_group')
                                ->label('Criar categoria automática com o nome do grupo?')
                                ->default(true)
                                ->reactive(),
                            Select::make('category_id')
                                ->label('Categoria de Destino')
                                ->options(fn ($record) => \App\Models\ImeiServiceCategory::where('type', $record->group_type)->pluck('name', 'id'))
                                ->hidden(fn ($get) => $get('create_category_from_group'))
                                ->required(fn ($get) => !$get('create_category_from_group'))
                                ->searchable(),
                            \Filament\Forms\Components\Toggle::make('use_custom_markup')
                                ->label('Aplicar margem global?')
                                ->default(true)
                                ->live(),
                            TextInput::make('markup')
                                ->label('Margem Global (%)')
                                ->numeric()
                                ->default(20)
                                ->suffix('%')
                                ->visible(fn ($get) => $get('use_custom_markup'))
                                ->live(),
                            \Filament\Forms\Components\Toggle::make('show_services')
                                ->label('Personalizar serviços individualmente?')
                                ->reactive(),
                            \Filament\Forms\Components\Repeater::make('services')
                                ->label('Serviços do Grupo')
                                ->schema([
                                    \Filament\Forms\Components\Hidden::make('id'),
                                    TextInput::make('service_name')->label('Nome')->disabled()->dehydrated(),
                                    TextInput::make('original_cost')->label('Custo Provedor')->disabled()->numeric(),
                                    TextInput::make('markup_percent')
                                        ->label('Margem (%)')
                                        ->numeric()
                                        ->suffix('%')
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $cost = (float) $get('original_cost');
                                            $set('final_price', number_format($cost * (1 + ($state / 100)), 2, '.', ''));
                                        }),
                                    TextInput::make('final_price')
                                        ->label('Preço Final ($)')
                                        ->numeric()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set, $get) {
                                            $cost = (float) $get('original_cost');
                                            if ($cost > 0) {
                                                $set('markup_percent', number_format((($state / $cost) - 1) * 100, 2, '.', ''));
                                            }
                                        }),
                                ])
                                ->hidden(fn ($get) => !$get('show_services'))
                                ->addable(false)
                                ->deletable(false)
                                ->itemLabel(fn (array $state): ?string => $state['service_name'] ?? null)
                                ->columns(3)
                                ->default(function (DhruCatalogService $record) {
                                    return \App\Models\DhruCatalogService::where('group_name', $record->group_name)
                                        ->where('dhru_provider_id', $record->dhru_provider_id)
                                        ->get()
                                        ->map(fn($s) => [
                                            'id' => $s->id,
                                            'service_name' => $s->service_name,
                                            'original_cost' => $s->cost,
                                            'markup_percent' => 20,
                                            'final_price' => number_format($s->cost * 1.20, 2, '.', ''),
                                        ])
                                        ->toArray();
                                }),
                        ])
                        ->action(function (DhruCatalogService $record, array $data, \App\Services\VitrineService $service) {
                            $categoryId = $data['category_id'] ?? null;
                            
                            if ($data['create_category_from_group'] ?? false) {
                                $nextCatOrder = \App\Models\ImeiServiceCategory::max('sort_order') + 1;
                                $category = \App\Models\ImeiServiceCategory::firstOrCreate(
                                    ['name' => $record->group_name],
                                    ['is_active' => true, 'type' => $record->group_type, 'sort_order' => $nextCatOrder]
                                );
                                $categoryId = $category->id;
                            }

                            if ($data['show_services'] ?? false) {
                                $count = $service->addBatchWithCustomData($data['services'], $categoryId);
                                $totalInGroup = count($data['services']);
                            } else {
                                $records = \App\Models\DhruCatalogService::query()
                                    ->where('group_name', $record->group_name)
                                    ->where('group_type', $record->group_type)
                                    ->where('dhru_provider_id', $record->dhru_provider_id)
                                    ->where('is_active', true)
                                    ->get();
                                $markup = ($data['use_custom_markup'] ?? false) ? (float) ($data['markup'] ?? 0) : 0;
                                $count = $service->addBatch($records, $categoryId, $markup);
                                $totalInGroup = $records->count();
                            }

                            $alreadyInVitrine = $totalInGroup - $count;
                            $body = "{$count} novos serviços adicionados.";
                            if ($alreadyInVitrine > 0) {
                                $body .= " ({$alreadyInVitrine} já estavam na vitrine e foram ignorados)";
                            }

                            \Filament\Notifications\Notification::make()
                                ->title("Processamento do Grupo Concluído")
                                ->body($body)
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\ViewAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->label('Ações'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('addToCustomBulk')
                        ->label('Adicionar Selecionados à Vitrine')
                        ->icon('heroicon-o-squares-plus')
                        ->color('success')
                        ->form([
                            Select::make('category_id')
                                ->label('Categoria de Destino')
                                ->options(\App\Models\ImeiServiceCategory::pluck('name', 'id'))
                                ->required(),
                            \Filament\Forms\Components\Toggle::make('use_custom_markup')
                                ->label('Aplicar margem global?')
                                ->default(true)
                                ->reactive(),
                            TextInput::make('markup')
                                ->label('Margem de Lucro Global (%)')
                                ->numeric()
                                ->default(20)
                                ->suffix('%')
                                ->visible(fn ($get) => $get('use_custom_markup')),
                        ])
                        ->action(function (\Illuminate\Support\Collection $records, array $data, \App\Services\VitrineService $service) {
                            $markup = ($data['use_custom_markup'] ?? false) ? (float) ($data['markup'] ?? 0) : 0;
                            $count = $service->addBatch($records, $data['category_id'], $markup);

                            \Filament\Notifications\Notification::make()
                                ->title("{$count} serviços adicionados à vitrine!")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDhruCatalogServices::route('/'),
            'view' => Pages\ViewDhruCatalogService::route('/{record}'),
        ];
    }
}
