<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Support\RegistrationSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $settings = RegistrationSettings::get();
        $customFields = RegistrationSettings::getCustomFields();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:users',
                $this->emailRule((bool) ($settings['real_email_validation'] ?? false)),
                $this->disposableEmailRule((bool) ($settings['real_email_validation'] ?? false)),
            ],
            'password' => $this->passwordRules(),
        ];

        if (($settings['phone_field_mode'] ?? 'hidden') !== 'hidden') {
            $rules['phone'] = [
                ($settings['phone_field_mode'] ?? 'hidden') === 'required' ? 'required' : 'nullable',
                'string',
                'max:30',
                $this->phoneRule(),
            ];
        }

        if (($settings['currency_field_mode'] ?? 'hidden') !== 'hidden') {
            $rules['preferred_currency'] = [
                ($settings['currency_field_mode'] ?? 'hidden') === 'required' ? 'required' : 'nullable',
                'string',
                'size:3',
                Rule::in(array_map('strtoupper', (array) ($settings['currency_options'] ?? []))),
            ];
        }

        if (($settings['location_mode'] ?? 'hidden') !== 'hidden') {
            $rules['country_code'] = ['nullable', 'string', 'size:2'];
            $rules['state_region'] = ['nullable', 'string', 'max:255'];
            $rules['city'] = ['nullable', 'string', 'max:255'];
        }

        if ((bool) ($settings['accept_terms_required'] ?? false)) {
            $rules['terms'] = ['accepted', 'required'];
        }

        foreach ($customFields as $field) {
            $fieldRules = [$field['required'] ? 'required' : 'nullable'];

            if ($field['type'] === 'email') {
                $fieldRules[] = 'email';
            } elseif ($field['type'] === 'url') {
                $fieldRules[] = 'url';
            } elseif (in_array($field['type'], ['textarea', 'text'], true)) {
                $fieldRules[] = 'string';
                $fieldRules[] = 'max:5000';
            } elseif ($field['type'] === 'select') {
                $fieldRules[] = Rule::in($field['options']);
            }

            $rules['registration_meta.' . $field['name']] = $fieldRules;
        }

        Validator::make($input, $rules)->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'country_code' => $input['country_code'] ?? null,
            'state_region' => $input['state_region'] ?? null,
            'city' => $input['city'] ?? null,
            'preferred_currency' => $input['preferred_currency'] ?? null,
            'registration_meta' => $this->extractRegistrationMeta($input, $customFields),
            'email_verified_at' => ($settings['email_verification_mode'] ?? 'required') === 'required' ? null : now(),
            'password' => Hash::make($input['password']),
        ]);

        return $user;
    }

    protected function emailRule(bool $strict): object
    {
        $rule = Rule::email();

        if ($strict) {
            $rule = $rule->validateMxRecord()->preventSpoofing();
        }

        return $rule;
    }

    protected function disposableEmailRule(bool $strict): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($strict): void {
            if (! $strict || ! is_string($value) || ! str_contains($value, '@')) {
                return;
            }

            $domain = strtolower(Str::after($value, '@'));

            if (in_array($domain, RegistrationSettings::getDisposableDomains(), true)) {
                $fail('Use um e-mail corporativo ou pessoal valido. Enderecos descartaveis nao sao permitidos.');
            }
        };
    }

    protected function phoneRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (blank($value)) {
                return;
            }

            $country = request()->input('country_code') ?: null;

            try {
                $util = PhoneNumberUtil::getInstance();
                $number = $util->parse((string) $value, $country);

                if (! $util->isValidNumber($number)) {
                    $fail('Informe um telefone valido no padrao internacional.');
                }
            } catch (NumberParseException) {
                $fail('Informe um telefone valido no padrao internacional.');
            }
        };
    }

    protected function extractRegistrationMeta(array $input, array $customFields): array
    {
        $meta = [];

        foreach ($customFields as $field) {
            $value = Arr::get($input, 'registration_meta.' . $field['name']);

            if (filled($value)) {
                $meta[$field['name']] = $value;
            }
        }

        return $meta;
    }
}
