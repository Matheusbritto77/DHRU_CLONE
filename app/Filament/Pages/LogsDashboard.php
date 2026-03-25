<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\Collection;
use App\Support\Monitoring\LogMonitoringService;

class LogsDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Suporte';
    protected static ?string $navigationLabel = 'Logs';
    protected static ?string $title = 'Logs';
    protected static string $view = 'filament.pages.logs-dashboard';
    protected static ?int $navigationSort = 40;

    public array $stats = [];
    public array $files = [];
    public array $entries = [];
    public string $selectedFile = '';
    public ?array $selectedEntry = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view-admin') ?? false;
    }

    public function mount(): void
    {
        $this->refreshData();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Atualizar')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->refreshData()),
        ];
    }

    public function refreshData(): void
    {
        $service = app(LogMonitoringService::class);
        $this->files = $service->listFiles();
        $this->selectedFile = $service->resolveSelectedFile($this->selectedFile, $this->files);
        $this->entries = $service->tailEntries($this->selectedFile);
        $this->stats = $service->stats($this->files, $this->entries);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('level')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->limit(140)
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->options([
                        'emergency' => 'emergency',
                        'alert' => 'alert',
                        'critical' => 'critical',
                        'error' => 'error',
                        'warning' => 'warning',
                        'notice' => 'notice',
                        'info' => 'info',
                        'debug' => 'debug',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('details')
                    ->label('Detalhes')
                    ->icon('heroicon-o-eye')
                    ->action(function (array $record): void {
                        $this->selectedEntry = $record;
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50]);
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $rows = collect($this->entries)
            ->values()
            ->map(fn (array $entry, int $index) => [
                'id' => $index + 1,
                'level' => $entry['level'],
                'message' => $entry['message'],
            ]);

        $model = new class extends \Illuminate\Database\Eloquent\Model {
            protected $guarded = [];
            public $timestamps = false;
            protected $table = 'logs_dashboard_virtual';
        };

        $query = $model->newQuery()->setQuery(
            app('db')->query()->fromSub(
                $rows->isEmpty()
                    ? app('db')->query()->selectRaw('1 as id, "" as level, "" as message')->whereRaw('1 = 0')
                    : $this->buildUnionQuery($rows),
                'logs_dashboard_virtual'
            )
        );

        return $query;
    }

    protected function buildUnionQuery(Collection $rows): \Illuminate\Database\Query\Builder
    {
        $query = null;

        foreach ($rows as $row) {
            $select = app('db')->query()->selectRaw('? as id, ? as level, ? as message', [
                $row['id'],
                $row['level'],
                $row['message'],
            ]);

            $query = $query ? $query->unionAll($select) : $select;
        }

        return $query;
    }
}
