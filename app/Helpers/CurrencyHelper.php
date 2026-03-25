<?php

namespace App\Helpers;

use App\Models\Currency;
use Illuminate\Support\Facades\Cache;

class CurrencyHelper
{
    /**
     * Converts an amount from the system base currency (usually USD) to the specified target currency.
     * Uses cache for 1 hour to avoid excessive DB calls.
     *
     * @param  float|decimal  $amount  The price in the base currency (e.g. from Dhru API)
     * @param  string|null  $targetCode  Target ISO code (e.g. BRL, EUR)
     * @return array  [converted_amount, symbol, code]
     */
    public static function convert($amount, ?string $targetCode = null): array
    {
        $targetCode = $targetCode ?: (auth()->user()?->preferred_currency ?: 'USD');
        
        $currency = Cache::remember("currency_data_{$targetCode}", 3600, function () use ($targetCode) {
            return Currency::where('code', $targetCode)->first() 
                ?: Currency::where('is_base', true)->first();
        });

        if (!$currency) {
            return [
                'amount' => $amount,
                'symbol' => '$',
                'code' => 'USD'
            ];
        }

        $converted = (float) $amount * (float) $currency->exchange_rate;

        return [
            'amount' => $converted,
            'symbol' => $currency->symbol,
            'code' => $currency->code,
            'formatted' => $currency->symbol . ' ' . number_format($converted, 2, ',', '.')
        ];
    }

    /**
     * Quick format string for localized display.
     */
    public static function format($amount, ?string $targetCode = null): string
    {
        return self::convert($amount, $targetCode)['formatted'];
    }
}
