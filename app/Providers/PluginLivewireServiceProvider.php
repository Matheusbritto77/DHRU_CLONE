<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;

class PluginLivewireServiceProvider extends ServiceProvider
{
    public function boot(): void
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
