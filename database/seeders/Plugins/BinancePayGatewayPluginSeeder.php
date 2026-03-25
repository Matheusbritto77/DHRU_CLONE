<?php

namespace Database\Seeders\Plugins;

use App\Plugins\Payments\Livewire\ManagePaymentGatewaySettings;

class BinancePayGatewayPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'payment-binance-pay';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Binance Pay',
            'type' => 'custom',
            'is_system' => true,
            'is_active' => true,
            'icon' => 'heroicon-o-currency-dollar',
            'description' => 'Gateway Binance Pay.',
            'has_admin_panel' => true,
            'admin_navigation_group' => 'Gateways Pagamentos',
            'admin_navigation_label' => 'Binance Pay',
            'admin_navigation_icon' => 'heroicon-o-currency-dollar',
            'admin_page_title' => 'Configurar Binance Pay',
            'admin_component_class' => ManagePaymentGatewaySettings::class,
            'default_settings' => [
                'enabled' => false,
                'display_name' => 'Binance Pay',
                'checkout_label' => 'Binance Pay',
                'manual_checkout' => false,
                'manual_checkout_url' => '',
                'manual_checkout_message' => '',
                'gateway_fee_rate' => 0,
                'gateway_fixed_fee' => 0,
                'reconcile_min_age_minutes' => 5,
                'reconcile_max_age_minutes' => 30,
                'reconcile_webhook_secret' => '',
                'checkout_currency' => 'USDT',
                'api_key' => '',
                'api_secret' => '',
                'currency' => 'USDT',
                'description' => 'Recarga de creditos',
                'return_url' => '',
            ],
        ];
    }
}
