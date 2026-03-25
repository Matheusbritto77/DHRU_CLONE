<?php

namespace App\Providers;

use App\Policies\AdminAccessPolicy;
use App\Policies\HorizonAccessPolicy;
use App\Policies\LogViewerAccessPolicy;
use App\Policies\PulseAccessPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AccessPolicyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('view-admin', app(AdminAccessPolicy::class)->view(...));
        Gate::define('viewHorizon', app(HorizonAccessPolicy::class)->view(...));
        Gate::define('viewPulse', app(PulseAccessPolicy::class)->view(...));
        Gate::define('view-pulse', app(PulseAccessPolicy::class)->view(...));
        Gate::define('viewLogViewer', app(LogViewerAccessPolicy::class)->view(...));
    }
}
