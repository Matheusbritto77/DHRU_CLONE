<?php

namespace Database\Seeders\Plugins;

class AuthRegisterHeaderPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'auth-register-header';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Cabecalho de Cadastro',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-user-plus',
            'description' => 'Cabecalho minimalista para a pagina de cadastro.',
            'default_settings' => [
                'eyebrow' => 'Nova conta',
                'title' => 'Crie sua conta',
                'subtitle' => 'Entre no ecossistema BR Server com uma experiencia limpa, rapida e focada no essencial.',
                'logo_src' => 'logo3.jpg',
            ],
            'blade_template' => '<section class="pt-12 px-6 md:px-10">
    <div class="max-w-6xl mx-auto">
        <a href="/" class="inline-flex items-center gap-3 mb-10">
            <img src="{{ asset($settings[\'logo_src\']) }}" class="h-12 w-12 rounded-2xl shadow-md" alt="Logo">
            <div>
                <p class="text-[11px] uppercase tracking-[0.35em] text-gray-400 dark:text-gray-500">{{ $settings[\'eyebrow\'] }}</p>
                <h1 class="text-3xl md:text-5xl font-semibold tracking-tight text-[#1d1d1f] dark:text-white">{{ $settings[\'title\'] }}</h1>
            </div>
        </a>
        <p class="max-w-2xl text-lg md:text-xl text-gray-500 dark:text-gray-400 leading-relaxed">{{ $settings[\'subtitle\'] }}</p>
    </div>
</section>',
        ];
    }
}
