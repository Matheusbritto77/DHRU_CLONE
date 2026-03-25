<?php

namespace App\Providers;

use App\Domain\Dhru\Clients\DhruFusionClient;
use App\Domain\Dhru\Contracts\DhruProviderClientInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DhruProviderClientInterface::class, DhruFusionClient::class);
    }
}
