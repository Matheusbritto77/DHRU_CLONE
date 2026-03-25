<?php

namespace App\Services;

use App\Models\Currency;
use App\Support\CurrencyCatalogService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    public function syncAll(): void
    {
        try {
            $baseCode = strtoupper(config('app.currency_base', 'USD'));
            $catalog = CurrencyCatalogService::getCurrencies();
            $rates = CurrencyCatalogService::getRates($baseCode);

            if (! isset($catalog[$baseCode])) {
                $catalog[$baseCode] = 'United States Dollar';
            }

            Currency::query()->update(['is_base' => false]);

            Currency::updateOrCreate(
                ['code' => $baseCode],
                [
                    'name' => $catalog[$baseCode] ?? $baseCode,
                    'symbol' => $this->resolveSymbol($baseCode),
                    'exchange_rate' => 1.0,
                    'is_base' => true,
                    'is_active' => true,
                ]
            );

            foreach ($catalog as $code => $name) {
                $code = strtoupper($code);

                Currency::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'symbol' => $this->resolveSymbol($code),
                        'exchange_rate' => $code === $baseCode ? 1.0 : (float) ($rates[$code] ?? 1.0),
                        'is_base' => $code === $baseCode,
                        'is_active' => true,
                    ]
                );

                Cache::forget("currency_data_{$code}");
            }

            Log::info('Currency Sync: catalog and exchange rates updated successfully.', [
                'base' => $baseCode,
                'currencies' => count($catalog),
            ]);
        } catch (\Exception $e) {
            Log::error('Currency Sync Exception: ' . $e->getMessage());
        }
    }

    protected function resolveSymbol(string $code): string
    {
        return [
            'USD' => '$',
            'BRL' => 'R$',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
            'JPY' => 'JPY',
            'AUD' => 'AUD',
            'CAD' => 'CAD',
            'CHF' => 'CHF',
            'CNY' => 'CNY',
        ][$code] ?? $code;
    }
}
