<?php

namespace Database\Seeders\Plugins;

class AuthLoginSupportPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'auth-login-support';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Apoio de Login',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-lifebuoy',
            'description' => 'Rodape auxiliar com dicas e suporte para a pagina de login.',
            'default_settings' => [
                'left_title' => 'Recuperacao de acesso',
                'left_text' => 'Se perdeu a senha, use o fluxo de recuperacao para redefinir seu acesso com seguranca.',
                'right_title' => 'Precisa de ajuda?',
                'right_text' => 'Fale com o suporte para duvidas de conta, pagamento ou acesso.',
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
