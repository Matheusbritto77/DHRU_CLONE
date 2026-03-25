<?php

namespace App\Filament\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use App\Support\Monitoring\HorizonMonitoringService;

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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Atualizar')
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->refreshData()),
            Action::make('pause')
                ->label('Pausar')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->requiresConfirmation()
                ->action(fn () => $this->runHorizonCommand('horizon:pause')),
            Action::make('continue')
                ->label('Continuar')
                ->icon('heroicon-o-play')
                ->color('success')
                ->action(fn () => $this->runHorizonCommand('horizon:continue')),
            Action::make('terminate')
                ->label('Reiniciar')
                ->icon('heroicon-o-stop')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->runHorizonCommand('horizon:terminate')),
        ];
    }

    public function refreshData(): void
    {
        try {
            $snapshot = app(HorizonMonitoringService::class)->snapshot();
            $this->stats = $snapshot['stats'];
            $this->masters = $snapshot['masters'];
            $this->workload = $snapshot['workload'];
            $this->recentJobs = $snapshot['recent_jobs'];
            $this->failedJobs = $snapshot['failed_jobs'];
            $this->error = null;
        } catch (Exception $exception) {
            $this->error = $exception->getMessage();
            $this->masters = [];
            $this->workload = [];
            $this->recentJobs = [];
            $this->failedJobs = [];
        }
    }

    protected function runHorizonCommand(string $command): void
    {
        try {
            Artisan::call($command);

            Notification::make()
                ->title('Comando executado com sucesso')
                ->success()
                ->send();

            $this->refreshData();
        } catch (Exception $exception) {
            Notification::make()
                ->title('Erro ao executar comando do Horizon')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
