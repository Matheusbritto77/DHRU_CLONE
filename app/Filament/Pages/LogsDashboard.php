<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LogsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Suporte';
    protected static ?string $navigationLabel = 'Logs';
    protected static ?string $title = 'Logs';
    protected static string $view = 'filament.pages.logs-dashboard';
    protected static ?int $navigationSort = 40;

    public array $stats = [];
    public array $files = [];
    public array $entries = [];

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
        $paths = collect(File::glob(storage_path('logs/*.log')))
            ->sortDesc()
            ->values();

        $this->files = $paths
            ->map(fn (string $path) => [
                'name' => basename($path),
                'size_kb' => round(File::size($path) / 1024, 2),
                'updated_at' => date('Y-m-d H:i:s', File::lastModified($path)),
            ])
            ->all();

        $activeLog = $paths->first();
        $lines = $activeLog ? file($activeLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        $tail = collect(array_slice($lines ?: [], -80))->reverse()->values();

        $this->entries = $tail->map(function (string $line) {
            $level = 'info';

            foreach (['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'] as $candidate) {
                if (Str::contains($line, ".{$candidate}:")) {
                    $level = strtolower($candidate);
                    break;
                }
            }

            return [
                'level' => $level,
                'message' => $line,
            ];
        })->all();

        $this->stats = [
            'files' => count($this->files),
            'latest_file' => $this->files[0]['name'] ?? '-',
            'errors' => collect($this->entries)->filter(fn ($entry) => in_array($entry['level'], ['error', 'critical', 'alert', 'emergency'], true))->count(),
            'warnings' => collect($this->entries)->where('level', 'warning')->count(),
        ];
    }
}
