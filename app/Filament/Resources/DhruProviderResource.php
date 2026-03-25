<?php

namespace App\Filament\Resources;

use App\Domain\Dhru\Services\SyncDhruProviderCatalogService;
use App\Filament\Resources\DhruProviderResource\Pages;
use App\Models\DhruProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DhruProviderResource extends Resource
{
    protected static ?string $model = DhruProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Dhru';

    protected static ?string $navigationLabel = 'Provedores';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificacao do Provedor')
                ->description('Cadastre aqui o provedor Dhru que vai substituir o uso legado por .env e arquivos estaticos.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome interno')
                        ->placeholder('Ex.: Provedor Principal')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug((string) $state))),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug tecnico')
                        ->helperText('Usado internamente para identificar o provedor.')
                        ->required()
                        ->unique(ignoreRecord: true),
                ])
                ->columns(2),
            Forms\Components\Section::make('Credenciais de Conexao')
                ->description('Informe a URL da API Dhru, o usuario e a API key exatamente como o provedor forneceu.')
                ->schema([
                    Forms\Components\TextInput::make('base_url')
                        ->label('URL do Servidor')
                        ->placeholder('https://seudominio.com')
                        ->helperText('O sistema gerencia automaticamente o sufixo /api/index.php. Pode informar apenas o domínio.')
                        ->required()
                        ->url()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('username')
                        ->label('Usuario da API')
                        ->placeholder('Seu usuario Dhru')
                        ->required(),
                    Forms\Components\Textarea::make('api_key')
                        ->label('API Key')
                        ->placeholder('Cole aqui a chave de acesso do provedor')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Forms\Components\Section::make('Sincronizacao')
                ->description('Controle como esse provedor participa da sincronizacao automatica do catalogo.')
                ->schema([
                    Forms\Components\TextInput::make('sync_interval_minutes')
                        ->label('Intervalo de sync em minutos')
                        ->numeric()
                        ->minValue(5)
                        ->default(60)
                        ->required(),
                    Forms\Components\Placeholder::make('connection_preview')
                        ->label('Resumo da conexao')
                        ->content(function (Get $get): string {
                            $baseUrl = trim((string) $get('base_url'));
                            $username = trim((string) $get('username'));

                            if ($baseUrl === '' || $username === '') {
                                return 'Preencha URL e usuario para revisar a conexao antes de salvar.';
                            }

                            return "Conectar em {$baseUrl} com o usuario {$username}.";
                        }),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Ativo para sincronizacao')
                        ->default(true),
                ])
                ->columns(2),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('base_url')->label('URL')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('last_sync_status')->badge(),
                Tables\Columns\TextColumn::make('services_count')->counts('services')->label('Servicos'),
                Tables\Columns\TextColumn::make('last_synced_at')->since(),
                Tables\Columns\TextColumn::make('sync_interval_minutes')->label('Sync (min)'),
            ])
            ->actions([
                Action::make('testConnection')
                    ->label('Testar conexao')
                    ->icon('heroicon-o-signal')
                    ->action(function (DhruProvider $record, SyncDhruProviderCatalogService $syncService): void {
                        $run = $syncService->sync($record);

                        Notification::make()
                            ->title($run->status === 'success' ? 'Conexao valida' : 'Falha na conexao')
                            ->body($run->message ?: 'Teste concluido.')
                            ->{$run->status === 'success' ? 'success' : 'danger'}()
                            ->send();
                    }),
                Action::make('sync')
                    ->label('Sincronizar')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (DhruProvider $record, SyncDhruProviderCatalogService $syncService): void {
                        $run = $syncService->sync($record);

                        Notification::make()
                            ->title("Sincronizacao {$run->status}")
                            ->body($run->message ?: 'Processo finalizado.')
                            ->{$run->status === 'success' ? 'success' : 'danger'}()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDhruProviders::route('/'),
            'create' => Pages\CreateDhruProvider::route('/create'),
            'edit' => Pages\EditDhruProvider::route('/{record}/edit'),
        ];
    }
}
