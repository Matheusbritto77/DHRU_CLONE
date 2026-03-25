<?php

namespace App\Support\Monitoring;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LogMonitoringService
{
    public function listFiles(): array
    {
        return collect(File::glob(storage_path('logs/*.log')))
            ->sortDesc()
            ->values()
            ->map(fn (string $path) => [
                'path' => $path,
                'name' => basename($path),
                'size_kb' => round(File::size($path) / 1024, 2),
                'updated_at' => date('Y-m-d H:i:s', File::lastModified($path)),
            ])
            ->all();
    }

    public function resolveSelectedFile(?string $selectedFile, array $files): string
    {
        if ($selectedFile && File::exists($selectedFile)) {
            return $selectedFile;
        }

        return $files[0]['path'] ?? '';
    }

    public function tailEntries(string $path, int $limit = 120): array
    {
        if (blank($path) || ! File::exists($path)) {
            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        return collect(array_slice($lines, -$limit))
            ->reverse()
            ->values()
            ->map(function (string $line, int $index) {
                $level = 'info';

                foreach (['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'] as $candidate) {
                    if (Str::contains($line, ".{$candidate}:")) {
                        $level = strtolower($candidate);
                        break;
                    }
                }

                return [
                    'id' => $index + 1,
                    'level' => $level,
                    'message' => $line,
                    'formatted' => $this->formatMessage($line),
                ];
            })
            ->all();
    }

    public function stats(array $files, array $entries): array
    {
        return [
            'files' => count($files),
            'latest_file' => $files[0]['name'] ?? '-',
            'errors' => collect($entries)->filter(fn ($entry) => in_array($entry['level'], ['error', 'critical', 'alert', 'emergency'], true))->count(),
            'warnings' => collect($entries)->where('level', 'warning')->count(),
        ];
    }

    protected function formatMessage(string $line): string
    {
        return preg_replace("/\\n|\\\\n/", PHP_EOL, $line) ?? $line;
    }
}
