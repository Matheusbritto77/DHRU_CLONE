<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dólar (Base Currency)
        Currency::updateOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'Dólar Americano',
                'symbol' => '$',
                'exchange_rate' => 1.0,
                'is_base' => true,
                'is_active' => true,
            ]
        );

        // 2. Real (BRL) - Updated via sync later
        Currency::updateOrCreate(
            ['code' => 'BRL'],
            [
                'name' => 'Real Brasileiro',
                'symbol' => 'R$',
                'exchange_rate' => 5.50, // Mock rate
                'is_base' => false,
                'is_active' => true,
            ]
        );

        // 3. Euro
        Currency::updateOrCreate(
            ['code' => 'EUR'],
            [
                'name' => 'Euro',
                'symbol' => '€',
                'exchange_rate' => 0.92,
                'is_base' => false,
                'is_active' => true,
            ]
        );
    }
}
