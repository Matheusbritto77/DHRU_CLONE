<?php

namespace App\Providers;

use App\Domain\Dhru\Clients\DhruFusionClient;
use App\Domain\Dhru\Contracts\DhruProviderClientInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DhruProviderClientInterface::class, DhruFusionClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        $this->registerPluginLivewireComponents();

        Gate::define('view-admin', function (User $user) {
            return (bool) $user->id_admin;
        });

        Gate::define('viewHorizon', function (?User $user = null) {
            return $user?->can('view-admin') ?? false;
        });

        Gate::define('viewPulse', function (?User $user = null) {
            return $user?->can('view-admin') ?? false;
        });

        Gate::define('view-pulse', function (User $user) {
            return $user->can('view-admin');
        });

        Gate::define('viewLogViewer', function (?User $user = null) {
            return $user?->can('view-admin') ?? false;
        });

        // Auto-start Bun Manager in local development
        if (app()->environment('local') && !app()->runningInConsole()) {
            $managerPath = base_path('manager.ts');
            if (file_exists($managerPath)) {
                @shell_exec("nohup bun run {$managerPath} > /dev/null 2>&1 &");
            }
        }
    }

    protected function registerPluginLivewireComponents(): void
    {
        $directory = app_path('Plugins');

        if (! File::isDirectory($directory)) {
            return;
        }

        foreach (File::allFiles($directory) as $file) {
            $relativePath = str_replace(
                [app_path() . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, '.php'],
                ['', '\\', ''],
                $file->getPathname()
            );

            $class = 'App\\' . $relativePath;

            if (! str_contains($class, '\\Livewire\\') || ! class_exists($class)) {
                continue;
            }

            $alias = collect(explode('\\', $relativePath))
                ->map(fn (string $segment): string => Str::kebab($segment))
                ->implode('.');

            Livewire::component($alias, $class);
        }
    }
}
