<?php

namespace Database\Seeders\Plugins;

use App\Plugins\Payments\Livewire\ManagePaymentGatewaySettings;

class MercadoPagoGatewayPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'payment-mercado-pago';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Mercado Pago',
            'type' => 'custom',
            'is_system' => true,
            'is_active' => true,
            'icon' => 'heroicon-o-credit-card',
            'description' => 'Gateway Mercado Pago.',
            'has_admin_panel' => true,
            'admin_navigation_group' => 'Gateways Pagamentos',
            'admin_navigation_label' => 'Mercado Pago',
            'admin_navigation_icon' => 'heroicon-o-credit-card',
            'admin_page_title' => 'Configurar Mercado Pago',
            'admin_component_class' => ManagePaymentGatewaySettings::class,
            'default_settings' => [
                'enabled' => false,
                'display_name' => 'Mercado Pago',
                'checkout_label' => 'Mercado Pago',
                'manual_checkout' => false,
                'manual_checkout_url' => '',
                'manual_checkout_message' => '',
                'gateway_fee_rate' => 0,
                'gateway_fixed_fee' => 0,
                'reconcile_min_age_minutes' => 5,
                'reconcile_max_age_minutes' => 30,
                'reconcile_webhook_secret' => '',
                'checkout_currency' => 'BRL',
                'mode' => 'production',
                'access_token' => '',
                'public_key' => '',
                'webhook_secret' => '',
                'success_url' => '',
                'failure_url' => '',
                'pending_url' => '',
            ],
        ];
    }
}
