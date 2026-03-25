<?php

namespace Database\Seeders\Plugins;

class DashboardSidebarPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'dashboard-sidebar';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Sidebar do Dashboard',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-squares-2x2',
            'description' => 'Sidebar lateral minimalista para areas autenticadas.',
            'default_settings' => [
                'logo_src' => 'logo3.jpg',
                'support_email' => 'contato@brserver.tech',
                'support_whatsapp' => '+55 (34) 99944-2627',
                'support_whatsapp_link' => 'https://wa.me/5534999442627',
            ],
            'blade_template' => <<<'BLADE'
<aside class="hidden lg:flex fixed inset-y-0 left-0 z-40 w-[320px] flex-col border-r" style="background: var(--theme-surface-strong); border-color: var(--theme-border); backdrop-filter: blur(24px);">
    <div class="px-8 py-8 border-b" style="border-color: var(--theme-border);">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-4">
            <img src="{{ asset($settings['logo_src']) }}" alt="Logo" class="h-14 w-14 rounded-2xl shadow-sm">
            <div>
                <p class="text-[11px] uppercase tracking-[0.28em] theme-muted">Painel</p>
                <p class="text-xl font-semibold tracking-tight theme-text">{{ config('app.name') }}</p>
            </div>
        </a>
    </div>

    <div class="px-6 py-6 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium {{ request()->routeIs('dashboard') ? 'theme-accent-bg shadow-sm' : 'theme-muted hover:opacity-80' }}" @if (! request()->routeIs('dashboard')) style="background: transparent;" onmouseover="this.style.background='var(--theme-surface-soft)'" onmouseout="this.style.background='transparent'" @endif>
            <i class="fas fa-chart-pie w-4"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('imei.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium theme-muted hover:opacity-80" onmouseover="this.style.background='var(--theme-surface-soft)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-mobile-alt w-4"></i>
            <span>IMEI Services</span>
        </a>
        <a href="{{ route('server-services') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium theme-muted hover:opacity-80" onmouseover="this.style.background='var(--theme-surface-soft)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-server w-4"></i>
            <span>Server Services</span>
        </a>
        <a href="{{ route('imei.history') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium theme-muted hover:opacity-80" onmouseover="this.style.background='var(--theme-surface-soft)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-clock-rotate-left w-4"></i>
            <span>Historico IMEI</span>
        </a>
        <a href="{{ route('Server.history') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium theme-muted hover:opacity-80" onmouseover="this.style.background='var(--theme-surface-soft)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-database w-4"></i>
            <span>Historico Server</span>
        </a>
        <a href="{{ route('add-credits') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium theme-muted hover:opacity-80" onmouseover="this.style.background='var(--theme-surface-soft)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-coins w-4"></i>
            <span>Adicionar creditos</span>
        </a>
    </div>

    <div class="mt-auto px-6 pb-6">
        <div class="rounded-[28px] p-5 space-y-4 theme-soft">
            <div>
                <p class="text-[11px] uppercase tracking-[0.25em] theme-muted">Conta</p>
                <p class="mt-2 text-lg font-semibold theme-text">{{ Auth::user()->name }}</p>
                <div class="mt-1 flex items-center gap-2">
                    <span class="text-sm font-bold theme-accent">
                        ${{ number_format(Auth::user()->credit ?? 0, 2) }}
                    </span>
                    <span class="h-1 w-1 rounded-full bg-gray-400"></span>
                    <p class="text-xs theme-muted">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="space-y-2 text-sm theme-muted">
                <a href="mailto:{{ $settings['support_email'] }}" class="flex items-center gap-2 transition hover:theme-accent" onmouseover="this.style.color='var(--theme-accent)'" onmouseout="this.style.color='var(--theme-text-muted)'">
                    <i class="fas fa-envelope w-4"></i>
                    <span>{{ $settings['support_email'] }}</span>
                </a>
                <a href="{{ $settings['support_whatsapp_link'] }}" class="flex items-center gap-2 transition" onmouseover="this.style.color='var(--theme-accent)'" onmouseout="this.style.color='var(--theme-text-muted)'">
                    <i class="fab fa-whatsapp w-4"></i>
                    <span>{{ $settings['support_whatsapp'] }}</span>
                </a>
            </div>
            <div class="flex gap-2 pt-2">
                <a href="{{ route('profile.show') }}" class="flex-1 rounded-2xl px-4 py-3 text-center text-sm font-medium transition theme-soft theme-text">
                    Perfil
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl px-4 py-3 text-sm font-medium transition" style="background: var(--theme-text); color: var(--theme-bg);">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>

<div class="lg:hidden sticky top-0 z-30 px-4 pt-4">
    <div class="rounded-[24px] px-5 py-4 flex items-center justify-between shadow-sm theme-panel-strong">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset($settings['logo_src']) }}" alt="Logo" class="h-10 w-10 rounded-2xl">
            <div>
                <p class="text-[10px] uppercase tracking-[0.25em] theme-muted">Painel</p>
                <p class="text-sm font-semibold tracking-tight theme-text">{{ config('app.name') }}</p>
            </div>
        </a>
        <a href="{{ route('profile.show') }}" class="h-11 w-11 rounded-2xl flex items-center justify-center theme-soft theme-text">
            <i class="fas fa-user"></i>
        </a>
    </div>
</div>
BLADE,
        ];
    }
}
