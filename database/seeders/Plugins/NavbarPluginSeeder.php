<?php

namespace Database\Seeders\Plugins;

class NavbarPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'navbar';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Barra de Navegação',
            'type' => 'navbar',
            'is_system' => true,
            'icon' => 'heroicon-o-bars-3',
            'description' => 'Menu principal com logo, links de Login e Registro.',
            'default_settings' => [
                'logo_src' => 'logo3.jpg',
                'font_family' => 'Inter',
                'bg_style' => 'rgba(255, 255, 255, 0.7)',
                'show_register' => true,
                'login_text' => 'Login',
                'register_text' => 'Registrar',
            ],
            'blade_template' => '<nav style="background: {{ $settings[\'bg_style\'] }}; font-family: {{ $settings[\'font_family\'] }}, sans-serif;" class="backdrop-blur-xl backdrop-saturate-150 sticky top-0 z-50 transition-all duration-300 border-b border-black/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14 items-center">
            <a href="/" class="flex-shrink-0 flex items-center">
                <img src="{{ asset($settings[\'logo_src\']) }}" class="h-8 w-auto rounded" alt="Logo">
            </a>
            <div class="flex items-center space-x-6">
                <a href="{{ route(\'login\') }}" class="text-sm font-medium text-[#1d1d1f] hover:text-[#0066cc] transition" style="text-decoration: none;">{{ $settings[\'login_text\'] }}</a>
                @if($settings[\'show_register\'] && Route::has(\'register\'))
                    <a href="{{ route(\'register\') }}" class="text-sm font-medium text-[#1d1d1f] hover:text-[#0066cc] transition" style="text-decoration: none;">{{ $settings[\'register_text\'] }}</a>
                @endif
            </div>
        </div>
    </div>
</nav>',
        ];
    }
}
