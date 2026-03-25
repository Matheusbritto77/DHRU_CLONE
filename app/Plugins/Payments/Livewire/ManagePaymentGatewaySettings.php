<?php

namespace App\Plugins\Payments\Livewire;

use App\Models\Plugin;
use App\Models\PluginAdminSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class ManagePaymentGatewaySettings extends Component implements HasForms
{
    use InteractsWithForms;

    public Plugin $plugin;
    public ?array $data = [];

    public function mount(Plugin $plugin): void
    {
        $this->plugin = $plugin;
        $settings = PluginAdminSetting::firstWhere('plugin_id', $plugin->id);
        $this->form->fill($settings?->settings ?? $plugin->default_settings ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('gateway_settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Geral')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')->label('Gateway ativo'),
                                Forms\Components\TextInput::make('display_name')->label('Nome exibido'),
                                Forms\Components\TextInput::make('checkout_label')->label('Label no checkout'),
                                Forms\Components\TextInput::make('checkout_currency')->label('Moeda do checkout')->default('BRL'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Credenciais')
                            ->schema($this->credentialFields()),
                        Forms\Components\Tabs\Tab::make('Reconciliacao')
                            ->schema([
                                Forms\Components\TextInput::make('reconcile_min_age_minutes')->numeric()->label('Idade minima em minutos'),
                                Forms\Components\TextInput::make('reconcile_max_age_minutes')->numeric()->label('Idade maxima em minutos'),
                                Forms\Components\TextInput::make('reconcile_webhook_secret')
                                    ->password()
                                    ->revealable()
                                    ->label('Segredo do webhook'),
                                Forms\Components\Placeholder::make('reconcile_webhook_url')
                                    ->label('Webhook de reconciliacao')
                                    ->content(fn (): string => route('payments.reconcile.webhook', ['gateway' => $this->plugin->slug])),
                                Forms\Components\Placeholder::make('events_webhook_url')
                                    ->label('Webhook de eventos')
                                    ->content(fn (): string => route('payments.events.webhook', ['gateway' => $this->plugin->slug])),
                            ]),
                        Forms\Components\Tabs\Tab::make('Runtime')
                            ->schema([
                                Forms\Components\Placeholder::make('checkout_url')
                                    ->label('Checkout unificado')
                                    ->content(fn (): string => route('payments.checkout', ['gateway' => $this->plugin->slug])),
                                Forms\Components\Placeholder::make('gateway_status')
                                    ->label('Status no kernel')
                                    ->content(fn (): string => ($this->data['enabled'] ?? false) ? 'Ativo e elegivel para checkout' : 'Desativado e oculto no checkout'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function credentialFields(): array
    {
        return match ($this->plugin->slug) {
            'payment-gerencianet-pix' => [
                Forms\Components\Select::make('mode')->options(['production' => 'production', 'sandbox' => 'sandbox']),
                Forms\Components\Toggle::make('debug')->label('Debug'),
                Forms\Components\TextInput::make('pix_key')->label('PIX key'),
                Forms\Components\TextInput::make('payment_request_message')->label('Mensagem do pagador'),
                Forms\Components\TextInput::make('pix_expiration_seconds')->numeric()->label('Expiracao do PIX (s)'),
                Forms\Components\TextInput::make('production_client_id')->label('Prod client id'),
                Forms\Components\TextInput::make('production_client_secret')->password()->label('Prod client secret'),
                Forms\Components\TextInput::make('production_certificate_name')->label('Prod certificate'),
                Forms\Components\TextInput::make('sandbox_client_id')->label('Sandbox client id'),
                Forms\Components\TextInput::make('sandbox_client_secret')->password()->label('Sandbox client secret'),
                Forms\Components\TextInput::make('sandbox_certificate_name')->label('Sandbox certificate'),
            ],
            'payment-binance-pay' => [
                Forms\Components\TextInput::make('api_key')->label('API key'),
                Forms\Components\TextInput::make('api_secret')->password()->label('API secret'),
                Forms\Components\TextInput::make('currency')->label('Moeda')->default('USDT'),
                Forms\Components\TextInput::make('description')->label('Descricao padrao'),
                Forms\Components\TextInput::make('return_url')->label('URL de retorno'),
            ],
            'payment-mercado-pago' => [
                Forms\Components\TextInput::make('access_token')->label('Access token'),
                Forms\Components\TextInput::make('public_key')->label('Public key'),
                Forms\Components\Select::make('mode')->options(['production' => 'production', 'sandbox' => 'sandbox']),
                Forms\Components\TextInput::make('success_url')->label('URL de sucesso'),
                Forms\Components\TextInput::make('failure_url')->label('URL de falha'),
                Forms\Components\TextInput::make('pending_url')->label('URL pendente'),
            ],
            'payment-stripe' => [
                Forms\Components\TextInput::make('secret_key')->label('Secret key'),
                Forms\Components\TextInput::make('publishable_key')->label('Publishable key'),
                Forms\Components\TextInput::make('webhook_secret')->label('Webhook secret'),
                Forms\Components\TextInput::make('success_url')->label('URL de sucesso'),
                Forms\Components\TextInput::make('cancel_url')->label('URL de cancelamento'),
            ],
            default => [],
        };
    }

    public function save(): void
    {
        PluginAdminSetting::updateOrCreate(
            ['plugin_id' => $this->plugin->id],
            ['settings' => $this->form->getState()]
        );

        Notification::make()
            ->title('Gateway salvo com sucesso')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('plugins.payments.livewire.manage-payment-gateway-settings');
    }
}
