<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LocalProcessManagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! app()->environment('local') || app()->runningInConsole()) {
            return;
        }

        $managerPath = base_path('manager.ts');

        if (file_exists($managerPath)) {
            @shell_exec("nohup bun run {$managerPath} > /dev/null 2>&1 &");
        }
    }
}
