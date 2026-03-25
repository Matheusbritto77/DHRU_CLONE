<?php

namespace App\Providers\Filament;

use App\Filament\Resources\PluginResource;
use App\Models\Plugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Navigation\NavigationItem;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path(env('FILAMENT_ADMIN_PATH', 'admin'))
            ->login()
            ->colors([
                'primary' => '#0071e3',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationItems(array_merge(
                $this->getPluginNavigationItems(),
                $this->getSupportNavigationItems()
            ))
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * @return array<int, NavigationItem>
     */
    protected function getPluginNavigationItems(): array
    {
        if (! Schema::hasTable('plugins') || ! Schema::hasColumn('plugins', 'has_admin_panel')) {
            return [];
        }

        return Plugin::query()
            ->where('has_admin_panel', true)
            ->whereIn('admin_panel_location', ['plugin', 'both'])
            ->orderBy('name')
            ->get()
            ->map(fn (Plugin $plugin): NavigationItem => NavigationItem::make($plugin->getAdminNavigationLabel())
                ->group($plugin->admin_navigation_group ?: 'Plugins')
                ->icon($plugin->admin_navigation_icon ?: 'heroicon-o-puzzle-piece')
                ->url(fn (): string => PluginResource::getUrl('manageAdmin', ['record' => $plugin]))
            )
            ->all();
    }

    /**
     * @return array<int, NavigationItem>
     */
    protected function getSupportNavigationItems(): array
    {
        return [
            NavigationItem::make('Pulse')
                ->group('Suporte')
                ->icon('heroicon-o-chart-bar-square')
                ->url('/pulse', shouldOpenInNewTab: true),
            
            NavigationItem::make('Horizon')
                ->group('Suporte')
                ->icon('heroicon-o-queue-list')
                ->url('/horizon', shouldOpenInNewTab: true),
            
            NavigationItem::make('Logs')
                ->group('Suporte')
                ->icon('heroicon-o-document-text')
                ->url('/log-viewer', shouldOpenInNewTab: true),
        ];
    }
}
