<?php

namespace App\Support;

use App\Models\Currency;
use App\Models\Plugin;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use libphonenumber\PhoneNumberUtil;
use Locale;

class RegistrationSettings
{
    public const PLUGIN_SLUG = 'auth-register-form';

    public static function defaults(): array
    {
        return [
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
            'currency_field_mode' => 'required',
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
        ];
    }

    public static function get(): array
    {
        $plugin = Plugin::where('slug', self::PLUGIN_SLUG)->first();
        $settings = array_replace_recursive(
            self::defaults(),
            $plugin?->default_settings ?? [],
            $plugin?->adminSettings?->settings ?? [],
        );

        $currencyOptions = self::getCurrencyOptions();

        if ($currencyOptions !== []) {
            $settings['currency_options'] = array_keys($currencyOptions);
        }

        // A moeda preferida faz parte obrigatoria do cadastro e nao deve ser desativada no plugin.
        $settings['currency_field_mode'] = 'required';

        return $settings;
    }

    public static function getCustomFields(): array
    {
        return collect(self::get()['custom_fields'] ?? [])
            ->filter(fn ($field) => filled(Arr::get($field, 'name')) && filled(Arr::get($field, 'label')))
            ->map(function (array $field): array {
                $name = Str::snake(Arr::get($field, 'name'));

                return [
                    'name' => $name,
                    'label' => Arr::get($field, 'label'),
                    'type' => Arr::get($field, 'type', 'text'),
                    'placeholder' => Arr::get($field, 'placeholder'),
                    'required' => (bool) Arr::get($field, 'required', false),
                    'options' => array_values(array_filter(
                        preg_split('/\r\n|\r|\n/', (string) Arr::get($field, 'options', '')) ?: []
                    )),
                ];
            })
            ->values()
            ->all();
    }

    public static function getCountryOptions(): array
    {
        $regions = PhoneNumberUtil::getInstance()->getSupportedRegions();
        sort($regions);

        $options = [];

        foreach ($regions as $region) {
            $label = class_exists(Locale::class)
                ? Locale::getDisplayRegion('-' . $region, app()->getLocale() ?: 'en')
                : $region;

            $options[$region] = $label ?: $region;
        }

        return $options;
    }

    public static function getDisposableDomains(): array
    {
        return [
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            'temp-mail.org',
            'tempmail.com',
            'yopmail.com',
            'sharklasers.com',
            'dispostable.com',
            'fakeinbox.com',
            'trashmail.com',
        ];
    }

    public static function getCurrencyOptions(): array
    {
        $activeCurrencies = Currency::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['code', 'name'])
            ->mapWithKeys(fn (Currency $currency) => [
                strtoupper($currency->code) => sprintf(
                    '%s - %s',
                    strtoupper($currency->code),
                    $currency->name ?: strtoupper($currency->code),
                ),
            ])
            ->all();

        if ($activeCurrencies !== []) {
            return $activeCurrencies;
        }

        return CurrencyCatalogService::getFormattedOptions();
    }
}
