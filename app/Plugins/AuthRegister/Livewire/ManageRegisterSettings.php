<?php

namespace App\Plugins\AuthRegister\Livewire;

use App\Models\Plugin;
use App\Models\PluginAdminSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Notifications\Notification;

class ManageRegisterSettings extends Component implements HasForms
{
    use InteractsWithForms;

    public Plugin $plugin;
    public ?array $data = [];

    public function mount(Plugin $plugin): void
    {
        $this->plugin = $plugin;
        
        $settings = PluginAdminSetting::firstWhere('plugin_id', $this->plugin->id);
        
        $this->form->fill($settings?->settings ?? $this->plugin->default_settings ?? []);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Interface')
                            ->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('card_title')->label('Título do Card'),
                                    Forms\Components\TextInput::make('card_subtitle')->label('Subtítulo do Card'),
                                ]),
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('name_placeholder')->label('Placeholder Nome'),
                                    Forms\Components\TextInput::make('email_placeholder')->label('Placeholder Email'),
                                    Forms\Components\TextInput::make('password_placeholder')->label('Placeholder Senha'),
                                ]),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('submit_text')->label('Texto Botão Submit'),
                                    Forms\Components\TextInput::make('login_text')->label('Texto Botão Login'),
                                ]),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Funcionalidades')
                            ->schema([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\Select::make('location_mode')
                                        ->options(['hidden' => 'Oculto', 'optional' => 'Opcional', 'required' => 'Obrigatório']),
                                    Forms\Components\Select::make('phone_field_mode')
                                        ->options(['hidden' => 'Oculto', 'optional' => 'Opcional', 'required' => 'Obrigatório']),
                                    Forms\Components\Select::make('currency_field_mode')
                                        ->options(['hidden' => 'Oculto', 'optional' => 'Opcional', 'required' => 'Obrigatório']),
                                ]),
                                Forms\Components\Toggle::make('real_email_validation')->label('Validar Email Real'),
                                Forms\Components\Toggle::make('accept_terms_required')->label('Obrigar aceitar termos'),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Termos de Uso')
                            ->schema([
                                Forms\Components\TextInput::make('terms_label')->label('Label do Checkbox'),
                                Forms\Components\RichEditor::make('terms_content')->label('Conteúdo dos Termos'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Email de Verificação')
                            ->schema([
                                Forms\Components\TextInput::make('verification_email_subject')->label('Assunto'),
                                Forms\Components\TextInput::make('verification_email_greeting')->label('Saudação'),
                                Forms\Components\Textarea::make('verification_email_intro')->label('Introdução'),
                                Forms\Components\TextInput::make('verification_email_button')->label('Texto do Botão'),
                            ]),
                    ])->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        PluginAdminSetting::updateOrCreate(
            ['plugin_id' => $this->plugin->id],
            ['settings' => $state]
        );

        Notification::make()
            ->title('Configurações de registro salvas!')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('plugins.auth-register.livewire.manage-register-settings');
    }
}
