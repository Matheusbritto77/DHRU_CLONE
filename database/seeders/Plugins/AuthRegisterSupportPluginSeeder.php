<?php

namespace Database\Seeders\Plugins;

class AuthRegisterSupportPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'auth-register-support';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Apoio de Cadastro',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-sparkles',
            'description' => 'Bloco auxiliar para a pagina de cadastro.',
            'default_settings' => [
                'left_title' => 'Comece em minutos',
                'left_text' => 'Crie a conta, valide seu e-mail e entre no painel com tudo preparado para operar sem friccao.',
                'right_title' => 'Visual minimalista',
                'right_text' => 'Interface limpa, espaçamento generoso e foco total na legibilidade, inspirada em produtos Apple.',
            ],
            'blade_template' => '<section class="px-6 md:px-10 pb-14">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-[24px] border border-gray-100 dark:border-gray-800 bg-white/70 dark:bg-white/5 backdrop-blur-xl p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500 mb-3">{{ $settings[\'left_title\'] }}</p>
            <p class="text-[15px] leading-7 text-gray-600 dark:text-gray-300">{{ $settings[\'left_text\'] }}</p>
        </div>
        <div class="rounded-[24px] border border-gray-100 dark:border-gray-800 bg-white/70 dark:bg-white/5 backdrop-blur-xl p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500 mb-3">{{ $settings[\'right_title\'] }}</p>
            <p class="text-[15px] leading-7 text-gray-600 dark:text-gray-300">{{ $settings[\'right_text\'] }}</p>
        </div>
    </div>
</section>',
        ];
    }
}
