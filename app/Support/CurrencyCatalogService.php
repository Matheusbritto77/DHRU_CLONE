<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyCatalogService
{
    public const API_BASE = 'https://api.frankfurter.dev/v1';

    public static function getCurrencies(): array
    {
        return Cache::remember('currency_catalog.currencies', now()->addDay(), function (): array {
            $response = Http::timeout(10)->get(self::API_BASE . '/currencies');

            if (! $response->successful()) {
                return self::fallbackCurrencies();
            }

            $data = $response->json();

            if (! is_array($data) || $data === []) {
                return self::fallbackCurrencies();
            }

            ksort($data);

            return $data;
        });
    }

    public static function getFormattedOptions(): array
    {
        return collect(self::getCurrencies())
            ->mapWithKeys(fn (string $name, string $code) => [$code => "{$code} - {$name}"])
            ->all();
    }

    public static function getRates(string $base = 'USD'): array
    {
        $base = strtoupper($base);

        return Cache::remember("currency_catalog.rates.{$base}", now()->addHours(6), function () use ($base): array {
            $response = Http::timeout(10)->get(self::API_BASE . '/latest', [
                'base' => $base,
            ]);

            if (! $response->successful()) {
                return [];
            }

            $data = $response->json();

            return is_array($data['rates'] ?? null) ? $data['rates'] : [];
        });
    }

    public static function getSelectedCurrencySummaries(array $codes, string $base = 'USD'): array
    {
        $currencies = self::getCurrencies();
        $rates = self::getRates($base);

        return collect($codes)
            ->filter()
            ->map(function (string $code) use ($currencies, $rates, $base): array {
                $code = strtoupper($code);

                return [
                    'code' => $code,
                    'name' => $currencies[$code] ?? $code,
                    'rate' => $code === strtoupper($base) ? 1.0 : ($rates[$code] ?? null),
                ];
            })
            ->values()
            ->all();
    }

    protected static function fallbackCurrencies(): array
    {
        return [
            'AUD' => 'Australian Dollar',
            'BRL' => 'Brazilian Real',
            'CAD' => 'Canadian Dollar',
            'CHF' => 'Swiss Franc',
            'CNY' => 'Chinese Renminbi Yuan',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'MXN' => 'Mexican Peso',
            'USD' => 'US Dollar',
        ];
    }
}
