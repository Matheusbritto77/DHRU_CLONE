<?php

namespace Database\Seeders\Plugins;

use App\Plugins\AuthRegister\Livewire\ManageRegisterSettings;

class AuthRegisterFormPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'auth-register-form';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Formulario de Cadastro',
            'type' => 'custom',
            'is_system' => true,
            'icon' => 'heroicon-o-identification',
            'description' => 'Formulario principal de criacao de conta.',
            'has_admin_panel' => true,
            'admin_navigation_group' => 'Autenticacao',
            'admin_navigation_label' => 'Cadastro',
            'admin_navigation_icon' => 'heroicon-o-user-plus',
            'admin_page_title' => 'Gerenciar cadastro',
            'admin_component_class' => ManageRegisterSettings::class,
            'default_settings' => [
                'card_title' => 'Cadastro',
                'card_subtitle' => 'Crie sua conta para acessar pedidos, creditos e recursos da plataforma.',
                'name_placeholder' => 'Nome completo',
                'email_placeholder' => 'E-mail',
                'phone_placeholder' => 'Telefone',
                'password_placeholder' => 'Senha',
                'password_confirmation_placeholder' => 'Confirmar senha',
                'currency_label' => 'Moeda preferida',
                'submit_text' => 'Criar conta',
                'login_text' => 'Entrar agora',
                'location_mode' => 'hidden',
                'email_verification_mode' => 'required',
                'phone_field_mode' => 'hidden',
                'currency_field_mode' => 'hidden',
                'currency_base' => 'USD',
                'currency_options' => ['USD', 'EUR', 'BRL'],
                'real_email_validation' => false,
                'accept_terms_required' => false,
                'verification_email_subject' => 'Verifique seu e-mail',
                'verification_email_greeting' => 'Confirme seu cadastro',
                'verification_email_intro' => 'Obrigado por criar sua conta. Antes de continuar, confirme seu e-mail clicando no botao abaixo.',
                'verification_email_button' => 'Verificar e-mail',
                'verification_email_outro' => 'Se voce nao criou esta conta, ignore esta mensagem.',
                'terms_label' => 'Li e aceito os termos de uso.',
                'terms_content' => '<p>Descreva aqui os termos aceitos no cadastro.</p>',
                'custom_fields' => [],
            ],
            'blade_template' => <<<'BLADE'
@php
    $countryOptions = \App\Support\RegistrationSettings::getCountryOptions();
    $customFields = \App\Support\RegistrationSettings::getCustomFields();
    $locationEnabled = ($settings['location_mode'] ?? 'hidden') !== 'hidden';
    $phoneMode = $settings['phone_field_mode'] ?? 'hidden';
    $currencyMode = $settings['currency_field_mode'] ?? 'hidden';
    $currencyOptions = collect(\App\Support\RegistrationSettings::getCurrencyOptions())
        ->only(array_map('strtoupper', $settings['currency_options'] ?? []))
        ->all();
    $termsEnabled = (bool) ($settings['accept_terms_required'] ?? false);
@endphp

<section class="px-6 md:px-10 py-10">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-[0.95fr_1.05fr] gap-8 items-start">
        <div class="rounded-[32px] border border-white/60 dark:border-white/10 bg-[radial-gradient(circle_at_top_left,_rgba(0,113,227,0.14),_transparent_38%),linear-gradient(135deg,rgba(255,255,255,0.92),rgba(255,255,255,0.72))] dark:bg-[radial-gradient(circle_at_top_left,_rgba(0,113,227,0.16),_transparent_38%),linear-gradient(135deg,rgba(29,29,31,0.92),rgba(22,22,23,0.76))] backdrop-blur-2xl p-8 md:p-10 shadow-[0_20px_60px_rgba(0,0,0,0.08)]">
            <div class="space-y-4">
                <div class="rounded-[24px] bg-white/80 dark:bg-white/5 border border-white/70 dark:border-white/10 p-6">
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500 mb-3">Conta unificada</p>
                    <p class="text-[15px] leading-7 text-[#1d1d1f] dark:text-gray-200">Cadastre-se uma vez e centralize acesso, historico, creditos e gerenciamento.</p>
                </div>
                <div class="rounded-[24px] bg-white/80 dark:bg-white/5 border border-white/70 dark:border-white/10 p-6">
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400 dark:text-gray-500 mb-3">Cadastro adaptavel</p>
                    <p class="text-[15px] leading-7 text-[#1d1d1f] dark:text-gray-200">Telefone, localizacao, termos, verificacao e campos extras podem ser ajustados pelo administrador.</p>
                </div>
            </div>
        </div>

        <div class="w-full px-8 py-8 bg-white/80 dark:bg-[#1d1d1f]/80 backdrop-blur-xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden rounded-[28px]">
            <div class="text-center mb-8">
                <h3 class="text-[28px] font-semibold text-apple-dark dark:text-white tracking-tight">{{ $settings['card_title'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-[15px] mt-1 font-light">{{ $settings['card_subtitle'] }}</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 text-red-600 text-sm text-center bg-red-50 dark:bg-red-900/30 p-3 rounded-xl border border-red-100 dark:border-red-900">
                        <ul class="list-none space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="{{ $settings['name_placeholder'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                </div>

                <div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="{{ $settings['email_placeholder'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                </div>

                @if ($phoneMode !== 'hidden')
                    <div>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" @if($phoneMode === 'required') required @endif autocomplete="tel" placeholder="{{ $settings['phone_placeholder'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                    </div>
                @endif

                @if ($currencyMode !== 'hidden')
                    <div>
                        <select id="preferred_currency" name="preferred_currency" @if($currencyMode === 'required') required @endif class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none">
                            <option value="">{{ $settings['currency_label'] }}</option>
                            @foreach ($currencyOptions as $currencyCode => $currencyLabel)
                                <option value="{{ $currencyCode }}" @selected(old('preferred_currency') === $currencyCode)>{{ $currencyLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($locationEnabled)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <select id="country_code" name="country_code" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none">
                                <option value="">Pais</option>
                                @foreach ($countryOptions as $countryCode => $countryLabel)
                                    <option value="{{ $countryCode }}" @selected(old('country_code') === $countryCode)>{{ $countryLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input id="state_region" type="text" name="state_region" value="{{ old('state_region') }}" placeholder="Estado / Regiao" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                        </div>
                        <div>
                            <input id="city" type="text" name="city" value="{{ old('city') }}" placeholder="Cidade" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                        </div>
                    </div>
                @endif

                @foreach ($customFields as $field)
                    <div>
                        @if ($field['type'] === 'textarea')
                            <textarea name="registration_meta[{{ $field['name'] }}]" @if($field['required']) required @endif placeholder="{{ $field['placeholder'] ?: $field['label'] }}" class="w-full px-4 py-3.5 min-h-[110px] bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none">{{ old('registration_meta.' . $field['name']) }}</textarea>
                        @elseif ($field['type'] === 'select')
                            <select name="registration_meta[{{ $field['name'] }}]" @if($field['required']) required @endif class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none">
                                <option value="">{{ $field['label'] }}</option>
                                @foreach ($field['options'] as $option)
                                    <option value="{{ $option }}" @selected(old('registration_meta.' . $field['name']) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ in_array($field['type'], ['email', 'url'], true) ? $field['type'] : 'text' }}" name="registration_meta[{{ $field['name'] }}]" value="{{ old('registration_meta.' . $field['name']) }}" @if($field['required']) required @endif placeholder="{{ $field['placeholder'] ?: $field['label'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                        @endif
                    </div>
                @endforeach

                <div>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="{{ $settings['password_placeholder'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                </div>

                <div>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ $settings['password_confirmation_placeholder'] }}" class="w-full px-4 py-3.5 bg-gray-50/50 dark:bg-black/30 border border-gray-200 dark:border-gray-700 text-apple-dark dark:text-gray-100 text-[16px] rounded-[14px] focus:ring-1 focus:ring-apple-blue focus:border-apple-blue transition-all outline-none" />
                </div>

                @if ($termsEnabled)
                    <div class="rounded-[18px] border border-gray-200 dark:border-gray-800 bg-gray-50/70 dark:bg-black/20 p-4 space-y-4">
                        <div class="prose prose-sm max-w-none dark:prose-invert text-left">
                            {!! $settings['terms_content'] !!}
                        </div>
                        <label class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-300">
                            <input type="checkbox" name="terms" value="1" required class="mt-1 rounded border-gray-300 text-apple-blue focus:ring-apple-blue">
                            <span>{{ $settings['terms_label'] }}</span>
                        </label>
                    </div>
                @endif

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 bg-[#0071e3] hover:bg-[#0077ED] active:bg-[#0051a8] text-white font-medium rounded-[14px] text-[17px] tracking-wide transition-all duration-200 shadow-sm outline-none">
                        {{ $settings['submit_text'] }}
                    </button>
                </div>
                
                @if (Route::has('login'))
                <div class="text-center mt-6">
                    <p class="text-sm border-t border-gray-100 dark:border-gray-800 pt-6 text-gray-500 dark:text-gray-400">
                        Ja possui conta? <a href="{{ route('login') }}" class="text-apple-blue hover:underline">{{ $settings['login_text'] }}</a>
                    </p>
                </div>
                @endif
            </form>
        </div>
    </div>
</section>
BLADE,
        ];
    }
}
