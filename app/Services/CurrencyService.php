<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Synchronize all active non-base currencies with the external API.
     */
    public function syncAll(): void
    {
        $base = Currency::where('is_base', true)->first();
        if (!$base) {
            Log::error('Currency Sync: Base currency not found.');
            return;
        }

        try {
            // Free API (exchange-api)
            $response = Http::get("https://api.exchangerate-api.com/v4/latest/{$base->code}");
            
            if (!$response->successful()) {
                Log::error('Currency Sync: API call failed.', ['status' => $response->status()]);
                return;
            }

            $rates = $response->json()['rates'] ?? [];
            $currencies = Currency::where('is_base', false)->where('is_active', true)->get();

            foreach ($currencies as $currency) {
                if (isset($rates[$currency->code])) {
                    $newRate = (float) $rates[$currency->code];
                    
                    if ($currency->exchange_rate != $newRate) {
                        $currency->update(['exchange_rate' => $newRate]);
                        // Clear specific currency cache
                        Cache::forget("currency_data_{$currency->code}");
                    }
                }
            }
            
            Log::info('Currency Sync: Success. All active rates updated.');
        } catch (\Exception $e) {
            Log::error('Currency Sync Exception: ' . $e->getMessage());
        }
    }
}
