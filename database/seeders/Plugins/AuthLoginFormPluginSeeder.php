<?php

namespace Database\Seeders\Plugins;

class AuthLoginFormPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'auth-login-form';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Formulario de Login',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-user-circle',
            'description' => 'Formulario principal de autenticacao da pagina de login.',
            'default_settings' => [
                'card_title' => 'Login',
                'card_subtitle' => 'Use seu e-mail e senha para continuar.',
                'email_placeholder' => 'E-mail',
                'password_placeholder' => 'Senha',
                'remember_text' => 'Lembrar-me',
                'forgot_text' => 'Esqueceu a senha?',
                'submit_text' => 'Fazer Login',
                'register_text' => 'Crie agora',
            ],
            'blade_template' => '<section class="px-6 md:px-10 py-10">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-[1.15fr_0.85fr] gap-8 items-start">
        <div class="rounded-[32px] border border-white/60 dark:border-white/10 bg-[radial-gradient(circle_at_top_left,_rgba(0,113,227,0.14),_transparent_38%),linear-gradient(135deg,rgba(255,255,255,0.92),rgba(255,255,255,0.72))] dark:bg-[radial-gradient(circle_at_top_left,_rgba(0,113,227,0.16),_transparent_38%),linear-gradient(135deg,rgba(29,29,31,0.92),rgba(22,22,23,0.76))] backdrop-blur-2xl p-8 md:p-10 shadow-[0_20px_60px_rgba(0,0,0,0.08)]">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-[24px] bg-white/80 dark:bg-white/5 border border-white/70 dark:border-white/10 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500 mb-2">Seguranca</p>
                    <p class="text-sm text-[#1d1d1f] dark:text-gray-200">Sessao protegida com autenticacao do Laravel Fortify.</p>
                </div>
                <div class="rounded-[24px] bg-white/80 dark:bg-white/5 border border-white/70 dark:border-white/10 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500 mb-2">Acesso</p>
                    <p class="text-sm text-[#1d1d1f] dark:text-gray-200">Continue de onde voce parou e acompanhe suas ordens.</p>
                </div>
                <div class="rounded-[24px] bg-white/80 dark:bg-white/5 border border-white/70 dark:border-white/10 p-5">
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500 mb-2">Suporte</p>
                    <p class="text-sm text-[#1d1d1f] dark:text-gray-200">Equipe pronta para ajudar quando voce precisar.</p>
                </div>
            </div>
        </div>

        <div class="w-full px-8 py-8 bg-white/80 dark:bg-[#1d1d1f]/80 backdrop-blur-xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden rounded-[28px]">
            <div class="text-center mb-8">
                <h3 class="text-[28px] font-semibold text-apple-dark dark:text-white tracking-tight">{{ $settings[\'card_title\'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-[15px] mt-1 font-light">{{ $settings[\'card_subtitle\'] }}</p>
            </div>

            <form method="POST" action="{{ route(\'login\') }}" class="space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 text-red-600 text-sm text-center bg-red-50 dark:bg-red-900/30 p-3 rounded-xl border border-red-100 dark:border-red-900">
                        <ul class="list-none">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session(\'status\'))
                    <div class="mb-4 font-medium text-sm text-green-600 text-center bg-green-50 dark:bg-green-900/30 p-3 rounded-xl">
                        {{ session(\'status\') }}
                    </div>
                @endif

                <div>
                    <input id="email" type="email" name="email" value="{{ old(\'email\') }}" required autofocus autocomplete="username" placeholder="{{ $settings[\'email_placeholder\'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                </div>

                <div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="{{ $settings[\'password_placeholder\'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                </div>

                <div class="flex items-center justify-between pt-1 pb-2">
                    <label class="flex items-center text-gray-500 dark:text-gray-400 cursor-pointer group">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-apple-blue bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-apple-blue focus:ring-2 focus:ring-offset-0 transition-colors cursor-pointer">
                        <span class="ml-2 text-[14px] group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors">{{ $settings[\'remember_text\'] }}</span>
                    </label>

                    @if (Route::has(\'password.request\'))
                        <a class="text-[14px] text-apple-blue hover:text-blue-700 dark:hover:text-blue-400 transition-colors" href="{{ route(\'password.request\') }}">
                            {{ $settings[\'forgot_text\'] }}
                        </a>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 bg-[#0071e3] hover:bg-[#0077ED] active:bg-[#0051a8] text-white font-medium rounded-[14px] text-[17px] tracking-wide transition-all duration-200 shadow-sm outline-none">
                        {{ $settings[\'submit_text\'] }}
                    </button>
                </div>
                
                @if (Route::has(\'register\'))
                <div class="text-center mt-6">
                    <p class="text-sm border-t border-gray-100 dark:border-gray-800 pt-6 text-gray-500 dark:text-gray-400">
                        Ainda nao tem conta? <a href="{{ route(\'register\') }}" class="text-apple-blue hover:underline">{{ $settings[\'register_text\'] }}</a>
                    </p>
                </div>
                @endif
            </form>
        </div>
    </div>
</section>',
        ];
    }
}
