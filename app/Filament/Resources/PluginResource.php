<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PluginResource\Pages\ListPlugins;
use App\Filament\Resources\PluginResource\Pages\CreatePlugin;
use App\Filament\Resources\PluginResource\Pages\EditPlugin;
use App\Filament\Resources\PluginResource\Pages\ManagePluginAdmin;
use App\Models\Plugin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PluginResource extends Resource
{
    protected static ?string $model = Plugin::class;
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $navigationLabel = 'Plugins';
    protected static ?string $modelLabel = 'Plugin';
    protected static ?string $pluralModelLabel = 'Plugins';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Plugin Configuration')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Informações Básicas')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nome do Plugin')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('type')->label('Tipo')->placeholder('Ex: widget, auth, tool'),
                                Forms\Components\TextInput::make('version')->label('Versão')->default('1.0.0'),
                                Forms\Components\TextInput::make('author')->label('Autor'),
                            ]),
                            Forms\Components\Textarea::make('description')
                                ->label('Descrição')
                                ->columnSpanFull(),
                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
                                Forms\Components\Toggle::make('is_system')->label('Sistema (Protegido)')->default(false),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Visual e Template')
                        ->icon('heroicon-o-paint-brush')
                        ->schema([
                            Forms\Components\TextInput::make('blade_template')
                                ->label('Path para Blade Template')
                                ->placeholder('plugins.my-plugin.view')
                                ->required(),
                            Forms\Components\Textarea::make('css')
                                ->label('CSS Customizado')
                                ->rows(5),
                            Forms\Components\Textarea::make('js')
                                ->label('JS Customizado')
                                ->rows(5),
                        ]),

                    Forms\Components\Tabs\Tab::make('Administração')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Forms\Components\Toggle::make('has_admin_panel')
                                ->label('Possui Painel Admin próprio?')
                                ->reactive(),
                            
                            Forms\Components\Grid::make(3)
                                ->visible(fn ($get) => $get('has_admin_panel'))
                                ->schema([
                                    Forms\Components\Select::make('admin_panel_location')
                                        ->label('Localização')
                                        ->options([
                                            'plugin' => 'Apenas no Plugin',
                                            'admin'  => 'Apenas no Admin',
                                            'both'   => 'Ambos',
                                        ])->default('admin'),
                                    Forms\Components\TextInput::make('admin_navigation_group')->label('Grupo Menu'),
                                    Forms\Components\TextInput::make('admin_navigation_icon')->label('Ícone')->default('heroicon-o-cube'),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->visible(fn ($get) => $get('has_admin_panel'))
                                ->schema([
                                    Forms\Components\TextInput::make('admin_navigation_label')->label('Label Menu'),
                                    Forms\Components\TextInput::make('admin_page_title')->label('Título da Página'),
                                ]),

                            Forms\Components\TextInput::make('admin_component_class')
                                ->label('Livewire Component (Opcional)')
                                ->visible(fn ($get) => $get('has_admin_panel'))
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Configurações e Schema')
                        ->icon('heroicon-o-code-bracket')
                        ->schema([
                            Forms\Components\KeyValue::make('default_settings')
                                ->label('Configurações Padrão (JSON)')
                                ->keyLabel('Chave')
                                ->valueLabel('Valor Default')
                                ->addButtonLabel('+ Configuração'),
                            
                            Forms\Components\Section::make('Schema de Formulário Admin (Avançado)')
                                ->schema([
                                    Forms\Components\Textarea::make('admin_form_schema')
                                        ->label('Schema JSON')
                                        ->rows(15),
                                ])->collapsed()
                        ]),
                ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge()->color('info'),
                Tables\Columns\TextColumn::make('version')->label('Versão')->color('gray'),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo/Inativo')->boolean(),
                Tables\Columns\IconColumn::make('is_system')->label('Core')->boolean()->color('warning'),
                Tables\Columns\TextColumn::make('updated_at')->label('Atualizado')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Ativos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manage')
                    ->label('Gerenciar')
                    ->icon('heroicon-o-cog')
                    ->url(fn (Plugin $record) => ManagePluginAdmin::getUrl(['record' => $record]))
                    ->hidden(fn (Plugin $record) => !$record->has_admin_panel),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->hidden(fn () => !auth()->user()->is_admin), // Apenas precaução
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'       => ListPlugins::route('/'),
            'create'      => CreatePlugin::route('/create'),
            'edit'        => EditPlugin::route('/{record}/edit'),
            'manageAdmin' => ManagePluginAdmin::route('/{record}/manage'),
        ];
    }
}
