<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServerManagementResource\Pages;
use App\Models\ImeiServiceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServerManagementResource extends Resource
{
    protected static ?string $model = ImeiServiceCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-server';
    protected static ?string $navigationGroup = 'Gestão Comercial';
    protected static ?string $navigationLabel = 'Serviços Server';
    protected static ?string $modelLabel = 'Categoria Server';
    protected static ?string $pluralModelLabel = 'Categorias Server';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'SERVER');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações da Categoria Server')
                ->schema([
                    Forms\Components\Hidden::make('type')->default('SERVER'),
                    Forms\Components\TextInput::make('name')->label('Nome')->required(),
                    Forms\Components\Textarea::make('description')->label('Descrição')->columnSpanFull(),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordem')
                        ->numeric()
                        ->default(0)
                        ->unique(
                            table: 'imei_service_categories',
                            column: 'sort_order',
                            ignorable: fn ($record) => $record,
                            modifyRuleUsing: fn ($rule, $get) => $rule->where('type', $get('type'))
                        )
                        ->validationMessages([
                            'unique' => 'Esta posição na ordem já está sendo usada por outra categoria deste tipo.',
                        ]),
                    Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
                ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('availableServices'))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('available_services_count')->label('Serviços'),
                Tables\Columns\TextInputColumn::make('sort_order')
                    ->label('Ordem')
                    ->rules(function ($record) {
                        return [
                            'required',
                            'numeric',
                            'min:1',
                            \Illuminate\Validation\Rule::unique('imei_service_categories', 'sort_order')
                                ->where('type', $record->type)
                                ->ignore($record->id),
                        ];
                    }),
                Tables\Columns\ToggleColumn::make('is_active')->label('Ativo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Shared\RelationManagers\AvailableServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServerManagements::route('/'),
            'create' => Pages\CreateServerManagement::route('/create'),
            'edit' => Pages\EditServerManagement::route('/{record}/edit'),
        ];
    }
}
