<?php

namespace Database\Seeders\Plugins;

class TopbarPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'topbar';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Barra Superior (Contatos)',
            'type' => 'navbar',
            'is_system' => true,
            'icon' => 'heroicon-o-phone',
            'description' => 'Barra superior com email e Whatsapp de contato.',
            'default_settings' => [
                'email' => 'contato@brserver.tech',
                'whatsapp' => '+55 (34) 99944-2627',
                'whatsapp_link' => 'https://wa.me/5534999442627',
                'bg_color' => '#1d1d1f',
                'text_color' => '#ffffff',
            ],
            'blade_template' => '<div style="background-color: {{ $settings[\'bg_color\'] }}; color: {{ $settings[\'text_color\'] }};" class="text-xs py-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center text-center sm:text-left gap-2 sm:gap-0">
        <span class="tracking-wide text-[13px] font-light">Suporte: <a href="mailto:{{ $settings[\'email\'] }}" class="hover:opacity-70 transition">{{ $settings[\'email\'] }}</a></span>
        <span class="tracking-wide text-[13px] font-light">WhatsApp: <a href="{{ $settings[\'whatsapp_link\'] }}" class="hover:opacity-70 transition">{{ $settings[\'whatsapp\'] }}</a></span>
    </div>
</div>',
        ];
    }
}
