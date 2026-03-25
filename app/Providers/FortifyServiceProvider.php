<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Page;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::loginView(function () {
            $page = Page::where('slug', 'login')->where('is_active', true)->first();

            if (! $page) {
                return view('auth.login');
            }

            $blocks = $page->blocks()
                ->where('is_visible', true)
                ->with('plugin')
                ->orderBy('sort_order')
                ->get();

            return view('page-builder', compact('page', 'blocks'));
        });
        Fortify::registerView(function () {
            $page = Page::where('slug', 'register')->where('is_active', true)->first();

            if (! $page) {
                return view('auth.register');
            }

            $blocks = $page->blocks()
                ->where('is_visible', true)
                ->with('plugin')
                ->orderBy('sort_order')
                ->get();

            return view('page-builder', compact('page', 'blocks'));
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
