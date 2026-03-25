<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Support\Monitoring\PulseMonitoringService;

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
    public array $slowJobsCards = [];
    public array $slowRequestsCards = [];
    public array $exceptions = [];
    public array $outgoingRequests = [];

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
        $snapshot = app(PulseMonitoringService::class)->snapshot();
        $this->stats = $snapshot['stats'];
        $this->entryTypes = $snapshot['entry_types'];
        $this->slowJobsCards = $snapshot['slow_jobs_cards'];
        $this->slowRequestsCards = $snapshot['slow_requests_cards'];
        $this->exceptions = $snapshot['exceptions'];
        $this->outgoingRequests = $snapshot['outgoing_requests'];
    }
}
