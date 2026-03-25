<?php

namespace Database\Seeders\Plugins;

use App\Plugins\Payments\Livewire\ManagePaymentGatewaySettings;

class StripeGatewayPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'payment-stripe';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Stripe',
            'type' => 'custom',
            'is_system' => true,
            'is_active' => true,
            'icon' => 'heroicon-o-banknotes',
            'description' => 'Gateway Stripe.',
            'has_admin_panel' => true,
            'admin_navigation_group' => 'Gateways Pagamentos',
            'admin_navigation_label' => 'Stripe',
            'admin_navigation_icon' => 'heroicon-o-banknotes',
            'admin_page_title' => 'Configurar Stripe',
            'admin_component_class' => ManagePaymentGatewaySettings::class,
            'default_settings' => [
                'enabled' => false,
                'display_name' => 'Stripe',
                'checkout_label' => 'Stripe',
                'reconcile_min_age_minutes' => 5,
                'reconcile_max_age_minutes' => 30,
                'reconcile_webhook_secret' => '',
                'checkout_currency' => 'BRL',
                'secret_key' => '',
                'publishable_key' => '',
                'webhook_secret' => '',
                'success_url' => '',
                'cancel_url' => '',
            ],
        ];
    }
}
