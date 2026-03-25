<?php

namespace App\Support\Monitoring;

use Exception;
use Illuminate\Support\Collection;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;

class HorizonMonitoringService
{
    public function snapshot(): array
    {
        /** @var JobRepository $jobs */
        $jobs = app(JobRepository::class);
        /** @var MetricsRepository $metrics */
        $metrics = app(MetricsRepository::class);
        /** @var MasterSupervisorRepository $masters */
        $masters = app(MasterSupervisorRepository::class);
        /** @var WorkloadRepository $workload */
        $workload = app(WorkloadRepository::class);

        return [
            'stats' => [
                'status' => blank($masters->all()) ? 'inactive' : 'running',
                'jobs_per_minute' => $metrics->jobsProcessedPerMinute(),
                'recent_jobs' => $jobs->countRecent(),
                'failed_jobs' => $jobs->countRecentlyFailed(),
                'throughput' => $metrics->throughput(),
            ],
            'masters' => collect($masters->all())
                ->map(fn ($master) => [
                    'name' => $master->name,
                    'environment' => $master->environment,
                    'pid' => $master->pid,
                    'status' => $master->status,
                    'supervisors' => collect($master->supervisors)->implode(', '),
                ])
                ->all(),
            'workload' => collect($workload->get())->all(),
            'recent_jobs' => $this->normalizeJobs($jobs->getRecent()),
            'failed_jobs' => $this->normalizeJobs($jobs->getFailed()),
        ];
    }

    protected function normalizeJobs(Collection $jobs): array
    {
        return $jobs
            ->take(10)
            ->map(fn ($job) => [
                'id' => $job->id ?? null,
                'name' => class_basename($job->name ?? 'Unknown'),
                'queue' => $job->queue ?? '-',
                'status' => $job->status ?? '-',
                'completed_at' => $job->completed_at ?? null,
                'failed_at' => $job->failed_at ?? null,
            ])
            ->values()
            ->all();
    }
}
