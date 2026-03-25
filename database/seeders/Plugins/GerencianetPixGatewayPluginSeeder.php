<?php

namespace Database\Seeders\Plugins;

use App\Plugins\Payments\Livewire\ManagePaymentGatewaySettings;

class GerencianetPixGatewayPluginSeeder extends AbstractPluginSeeder
{
    protected function slug(): string
    {
        return 'payment-gerencianet-pix';
    }

    protected function definition(): array
    {
        return [
            'name' => 'Gerencianet PIX',
            'type' => 'custom',
            'is_system' => true,
            'is_active' => true,
            'icon' => 'heroicon-o-qr-code',
            'description' => 'Gateway PIX Gerencianet.',
            'has_admin_panel' => true,
            'admin_navigation_group' => 'Gateways Pagamentos',
            'admin_navigation_label' => 'Gerencianet PIX',
            'admin_navigation_icon' => 'heroicon-o-qr-code',
            'admin_page_title' => 'Configurar Gerencianet PIX',
            'admin_component_class' => ManagePaymentGatewaySettings::class,
            'default_settings' => [
                'enabled' => true,
                'display_name' => 'PIX',
                'checkout_label' => 'PIX',
                'mode' => 'production',
                'debug' => false,
                'pix_key' => '',
                'payment_request_message' => 'Pagamento Plataforma',
                'pix_expiration_seconds' => 3600,
                'reconcile_min_age_minutes' => 5,
                'reconcile_max_age_minutes' => 30,
                'reconcile_webhook_secret' => '',
                'checkout_currency' => 'BRL',
                'production_client_id' => '',
                'production_client_secret' => '',
                'production_certificate_name' => '',
                'sandbox_client_id' => '',
                'sandbox_client_secret' => '',
                'sandbox_certificate_name' => '',
            ],
        ];
    }
}
