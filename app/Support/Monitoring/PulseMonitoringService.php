<?php

namespace App\Support\Monitoring;

use Illuminate\Support\Facades\DB;

class PulseMonitoringService
{
    public function snapshot(): array
    {
        $entryTypes = DB::table('pulse_entries')
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

        $maxTypeTotal = max(array_column($entryTypes, 'total') ?: [1]);

        return [
            'stats' => [
                'entries' => DB::table('pulse_entries')->count(),
                'aggregates' => DB::table('pulse_aggregates')->count(),
                'exceptions' => DB::table('pulse_entries')->where('type', 'exception')->count(),
                'slow_jobs' => DB::table('pulse_entries')->where('type', 'slow_job')->count(),
                'slow_requests' => DB::table('pulse_entries')->where('type', 'slow_outgoing_request')->count(),
            ],
            'entry_types' => collect($entryTypes)
                ->map(fn ($item) => $item + ['width' => max(8, (int) round(($item['total'] / $maxTypeTotal) * 100))])
                ->all(),
            'slow_jobs_cards' => $this->slowJobCards(),
            'slow_requests_cards' => $this->slowOutgoingRequestCards(),
            'exceptions' => $this->recentExceptions(),
            'outgoing_requests' => $this->recentOutgoingRequests(),
        ];
    }

    protected function slowJobCards(): array
    {
        return DB::table('pulse_aggregates')
            ->select('key', DB::raw('MAX(CASE WHEN aggregate = "max" THEN value END) as max_value'))
            ->where('type', 'slow_job')
            ->groupBy('key')
            ->orderByDesc('max_value')
            ->limit(4)
            ->get()
            ->map(fn ($row) => [
                'label' => class_basename($row->key),
                'key' => $row->key,
                'max_ms' => (int) round((float) $row->max_value),
            ])
            ->all();
    }

    protected function slowOutgoingRequestCards(): array
    {
        return DB::table('pulse_entries')
            ->select('key', DB::raw('MAX(value) as max_value'), DB::raw('COUNT(*) as total'))
            ->where('type', 'slow_outgoing_request')
            ->groupBy('key')
            ->orderByDesc('max_value')
            ->limit(4)
            ->get()
            ->map(function ($row) {
                $decoded = json_decode($row->key, true);

                return [
                    'label' => is_array($decoded) ? ($decoded[0] ?? 'Request') : 'Request',
                    'target' => is_array($decoded) ? ($decoded[1] ?? $row->key) : $row->key,
                    'max_ms' => (int) round((float) $row->max_value),
                    'total' => (int) $row->total,
                ];
            })
            ->all();
    }

    protected function recentExceptions(): array
    {
        return DB::table('pulse_entries')
            ->select(['id', 'timestamp', 'key', 'value'])
            ->where('type', 'exception')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'recorded_at' => date('Y-m-d H:i:s', (int) $row->timestamp),
                'exception' => $row->key,
                'hits' => (int) $row->value,
            ])
            ->all();
    }

    protected function recentOutgoingRequests(): array
    {
        return DB::table('pulse_entries')
            ->select(['id', 'timestamp', 'key', 'value'])
            ->where('type', 'slow_outgoing_request')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $decoded = json_decode($row->key, true);

                return [
                    'id' => $row->id,
                    'recorded_at' => date('Y-m-d H:i:s', (int) $row->timestamp),
                    'method' => is_array($decoded) ? ($decoded[0] ?? '-') : '-',
                    'target' => is_array($decoded) ? ($decoded[1] ?? $row->key) : $row->key,
                    'duration_ms' => (int) $row->value,
                ];
            })
            ->all();
    }
}
