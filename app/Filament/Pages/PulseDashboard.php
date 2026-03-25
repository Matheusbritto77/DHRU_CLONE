<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class PulseDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Suporte';
    protected static ?string $navigationLabel = 'Pulse';
    protected static ?string $title = 'Pulse';
    protected static string $view = 'filament.pages.pulse-dashboard';
    protected static ?int $navigationSort = 20;

    public array $stats = [];
    public array $entryTypes = [];
    public array $latestEntries = [];
    public array $queueAggregates = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view-admin') ?? false;
    }

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $this->stats = [
            'entries' => DB::table('pulse_entries')->count(),
            'aggregates' => DB::table('pulse_aggregates')->count(),
            'exceptions' => DB::table('pulse_entries')->where('type', 'exception')->count(),
            'slow_jobs' => DB::table('pulse_entries')->where('type', 'slow_job')->count(),
        ];

        $this->entryTypes = DB::table('pulse_entries')
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'type' => $row->type,
                'total' => (int) $row->total,
            ])
            ->all();

        $this->latestEntries = DB::table('pulse_entries')
            ->select(['id', 'timestamp', 'type', 'key', 'value'])
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'recorded_at' => date('Y-m-d H:i:s', (int) $row->timestamp),
                'type' => $row->type,
                'key' => $row->key,
                'value' => $row->value,
            ])
            ->all();

        $this->queueAggregates = DB::table('pulse_aggregates')
            ->select(['bucket', 'type', 'key', 'aggregate', 'value', 'count'])
            ->whereIn('type', ['queued', 'processing', 'processed', 'slow_job'])
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'bucket_at' => date('Y-m-d H:i:s', (int) $row->bucket),
                'type' => $row->type,
                'key' => $row->key,
                'aggregate' => $row->aggregate,
                'value' => $row->value,
                'count' => $row->count,
            ])
            ->all();
    }
}
