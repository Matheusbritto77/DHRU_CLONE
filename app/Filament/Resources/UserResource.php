<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Support\RegistrationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Administração';
    protected static ?string $modelLabel = 'Usuário';
    protected static ?string $pluralModelLabel = 'Usuários';

    public static function form(Form $form): Form
    {
        $registrationSettings = RegistrationSettings::get();
        $customFields = RegistrationSettings::getCustomFields();

        return $form
            ->schema([
                Forms\Components\Section::make('Informações de Perfil')
                    ->schema([
                        Forms\Components\FileUpload::make('profile_photo_path')
                            ->label('Foto de Perfil')
                            ->image()
                            ->avatar()
                            ->directory('profile-photos')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefone')
                            ->tel()
                            ->visible(fn (): bool => ($registrationSettings['phone_field_mode'] ?? 'hidden') !== 'hidden')
                            ->required(fn (): bool => ($registrationSettings['phone_field_mode'] ?? 'hidden') === 'required'),
                        Forms\Components\Select::make('preferred_currency')
                            ->label($registrationSettings['currency_label'] ?? 'Moeda preferida')
                            ->options(RegistrationSettings::getCurrencyOptions())
                            ->searchable()
                            ->visible(fn (): bool => ($registrationSettings['currency_field_mode'] ?? 'hidden') !== 'hidden')
                            ->required(fn (): bool => ($registrationSettings['currency_field_mode'] ?? 'hidden') === 'required'),
                        Forms\Components\Select::make('country_code')
                            ->label('País')
                            ->options(RegistrationSettings::getCountryOptions())
                            ->searchable()
                            ->visible(fn (): bool => ($registrationSettings['location_mode'] ?? 'hidden') !== 'hidden'),
                        Forms\Components\TextInput::make('state_region')
                            ->label('Estado / Região')
                            ->visible(fn (): bool => ($registrationSettings['location_mode'] ?? 'hidden') !== 'hidden'),
                        Forms\Components\TextInput::make('city')
                            ->label('Cidade')
                            ->visible(fn (): bool => ($registrationSettings['location_mode'] ?? 'hidden') !== 'hidden'),
                    ])->columns(2),

                Forms\Components\Section::make('Campos do Cadastro')
                    ->schema(static::buildRegistrationMetaFields($customFields))
                    ->visible(fn (): bool => $customFields !== [])
                    ->columns(2),

                Forms\Components\Section::make('Segurança e Status')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Nova Senha')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                        
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('id_admin')
                                    ->label('Administrador')
                                    ->onIcon('heroicon-m-shield-check')
                                    ->offIcon('heroicon-m-user')
                                    ->onColor('danger'),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Usuário Ativo')
                                    ->default(true)
                                    ->onColor('success'),
                                
                                Forms\Components\Toggle::make('two_factor_confirmed_at')
                                    ->label('2FA Ativo')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(fn ($state) => filled($state)),
                            ]),
                    ]),

                Forms\Components\Section::make('Financeiro')
                    ->schema([
                        Forms\Components\TextInput::make('credit')
                            ->label('Saldo de Créditos')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                    ]),
            ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $customFields
     * @return array<int, Forms\Components\Component>
     */
    protected static function buildRegistrationMetaFields(array $customFields): array
    {
        return collect($customFields)
            ->map(function (array $field) {
                $statePath = 'registration_meta.' . $field['name'];
                $label = $field['label'];
                $required = (bool) ($field['required'] ?? false);
                $placeholder = $field['placeholder'] ?? $label;

                $component = match ($field['type']) {
                    'textarea' => Forms\Components\Textarea::make($statePath),
                    'select' => Forms\Components\Select::make($statePath)->options(array_combine($field['options'], $field['options'])),
                    default => Forms\Components\TextInput::make($statePath)->type(in_array($field['type'], ['email', 'url'], true) ? $field['type'] : 'text'),
                };

                return $component
                    ->label($label)
                    ->placeholder($placeholder)
                    ->required($required);
            })
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo_url')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('id_admin')
                    ->label('Admin')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('credit')
                    ->label('Crédito')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Cadastro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('id_admin')
                    ->label('Apenas Admins'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
