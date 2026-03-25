<?php

namespace App\Filament\Pages;

use Exception;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;

class HorizonDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Suporte';
    protected static ?string $navigationLabel = 'Horizon';
    protected static ?string $title = 'Horizon';
    protected static string $view = 'filament.pages.horizon-dashboard';
    protected static ?int $navigationSort = 30;

    public array $stats = [];
    public array $masters = [];
    public array $workload = [];
    public array $recentJobs = [];
    public array $failedJobs = [];
    public ?string $error = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('view-admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('queue.default') === 'redis';
    }

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        try {
            /** @var JobRepository $jobs */
            $jobs = app(JobRepository::class);
            /** @var MetricsRepository $metrics */
            $metrics = app(MetricsRepository::class);
            /** @var MasterSupervisorRepository $masters */
            $masters = app(MasterSupervisorRepository::class);
            /** @var WorkloadRepository $workload */
            $workload = app(WorkloadRepository::class);

            $this->stats = [
                'status' => blank($masters->all()) ? 'inactive' : 'running',
                'jobs_per_minute' => $metrics->jobsProcessedPerMinute(),
                'recent_jobs' => $jobs->countRecent(),
                'failed_jobs' => $jobs->countRecentlyFailed(),
                'throughput' => $metrics->throughput(),
            ];

            $this->masters = collect($masters->all())
                ->map(fn ($master) => [
                    'name' => $master->name,
                    'environment' => $master->environment,
                    'pid' => $master->pid,
                    'status' => $master->status,
                    'supervisors' => collect($master->supervisors)->implode(', '),
                ])
                ->all();

            $this->workload = collect($workload->get())->all();
            $this->recentJobs = $this->normalizeJobs($jobs->getRecent());
            $this->failedJobs = $this->normalizeJobs($jobs->getFailed());
            $this->error = null;
        } catch (Exception $exception) {
            $this->error = $exception->getMessage();
            $this->masters = [];
            $this->workload = [];
            $this->recentJobs = [];
            $this->failedJobs = [];
        }
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
