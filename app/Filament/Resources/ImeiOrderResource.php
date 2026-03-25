<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImeiOrderResource\Pages;
use App\Models\ImeiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImeiOrderResource extends Resource
{
    protected static ?string $model = ImeiService::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Relatórios / Ordens';
    protected static ?string $navigationLabel = 'Ordens IMEI';
    protected static ?string $modelLabel = 'Ordem IMEI';
    protected static ?string $pluralModelLabel = 'Ordens IMEI';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('servicename')->required(),
            Forms\Components\TextInput::make('serviceid')->required(),
            Forms\Components\TextInput::make('cost')->numeric()->required(),
            Forms\Components\TextInput::make('referenceid')->required(),
            Forms\Components\TextInput::make('user_id')->numeric()->required(),
            Forms\Components\Textarea::make('IMEI')->required(),
            Forms\Components\TextInput::make('status')->numeric()->default(0),
            Forms\Components\Textarea::make('code'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('servicename')->searchable(),
                Tables\Columns\TextColumn::make('user_id')->label('User ID')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('cost')->money(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListImeiOrders::route('/'),
        ];
    }
}
