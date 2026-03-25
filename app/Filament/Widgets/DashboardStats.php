<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Plugin;
use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Usuários', User::count())
                ->icon('heroicon-o-users')
                ->description('Registrados no sistema')
                ->color('primary'),
            Stat::make('Plugins Ativos', Plugin::where('is_active', true)->count())
                ->icon('heroicon-o-puzzle-piece')
                ->description('Instalados e funcionando')
                ->color('success'),
            Stat::make('Páginas Publicadas', Page::where('is_active', true)->count())
                ->icon('heroicon-o-document-text')
                ->description('Renderizadas no frontend')
                ->color('warning'),
        ];
    }
}
