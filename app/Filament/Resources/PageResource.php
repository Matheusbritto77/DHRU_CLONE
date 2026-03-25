<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Models\Plugin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $navigationLabel = 'Páginas do Site';
    protected static ?string $modelLabel = 'Página';
    protected static ?string $pluralModelLabel = 'Páginas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações da Página')
                ->icon('heroicon-o-document-text')
                ->description('Defina o slug (URL), título e metadados.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('title')->label('Título')->required(),
                        Forms\Components\TextInput::make('slug')->label('Slug (URL)')->required()->unique(ignoreRecord: true)
                            ->helperText('A rota da página. Ex: "sobre" gera /sobre'),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_active')->label('Página Ativa')->default(true),
                        Forms\Components\Toggle::make('is_system')->label('Protegida (Core)')
                            ->helperText('Ativar impede exclusão via painel.')
                            ->disabled(fn (?Page $record) => $record?->is_system),
                    ]),
                    Forms\Components\KeyValue::make('meta')->label('Meta Tags (SEO)')
                        ->keyLabel('Propriedade')->valueLabel('Conteúdo')
                        ->addButtonLabel('+ Meta Tag'),
                ]),

            Forms\Components\Section::make('Blocos / Plugins da Página')
                ->icon('heroicon-o-squares-2x2')
                ->description('Arraste para reordenar. Cada bloco é um plugin com configurações personalizáveis.')
                ->schema([
                    Forms\Components\Repeater::make('blocks')
                        ->relationship()
                        ->label('')
                        ->reorderable()
                        ->reorderableWithButtons()
                        ->orderColumn('sort_order')
                        ->collapsible()
                        ->cloneable()
                        ->itemLabel(fn (array $state): ?string =>
                            Plugin::find($state['plugin_id'] ?? null)?->name ?? 'Novo Bloco'
                        )
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\Select::make('plugin_id')
                                    ->label('Plugin')
                                    ->options(Plugin::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $plugin = Plugin::find($state);
                                        if ($plugin && $plugin->default_settings) {
                                            $set('settings', $plugin->default_settings);
                                        }
                                    }),
                                Forms\Components\Toggle::make('is_visible')->label('Visível')->default(true),
                            ]),
                            Forms\Components\KeyValue::make('settings')
                                ->label('Configurações do Plugin (JSON)')
                                ->keyLabel('Propriedade')->valueLabel('Valor')
                                ->addButtonLabel('+ Campo')
                                ->columnSpanFull(),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('+ Adicionar Bloco/Plugin'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('URL')->searchable()
                    ->formatStateUsing(fn ($state) => '/' . $state)
                    ->url(fn (Page $record) => url($record->slug === 'welcome' ? '/' : $record->slug), shouldOpenInNewTab: true)
                    ->color('primary'),
                Tables\Columns\TextColumn::make('blocks_count')->counts('blocks')->label('Blocos'),
                Tables\Columns\IconColumn::make('is_system')->boolean()->label('Core'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Ativa'),
                Tables\Columns\TextColumn::make('updated_at')->label('Atualizado')->since(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Ativas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Page $record) => $record->is_system),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit'   => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
