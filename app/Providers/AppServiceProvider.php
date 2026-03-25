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
