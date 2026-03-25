<?php

namespace App\Filament\Resources\PluginResource\Pages;

use App\Filament\Resources\PluginResource;
use App\Models\PluginAdminSetting;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class ManagePluginAdmin extends EditRecord
{
    protected static string $resource = PluginResource::class;

    protected static string $view = 'filament.resources.plugin-resource.pages.manage-plugin-admin';

    public function getTitle(): string
    {
        return $this->record->admin_page_title ?: "Gerenciar {$this->record->name}";
    }

    public function mount($record): void
    {
        parent::mount($record);
        
        $pluginSettings = PluginAdminSetting::firstOrCreate(
            ['plugin_id' => $this->record->id],
            ['settings' => $this->record->default_settings ?? []]
        );

        $this->form->fill($pluginSettings->settings);
    }

    public function hasCustomAdminComponent(): bool
    {
        return !empty($this->record->admin_component_class) && class_exists($this->record->admin_component_class);
    }

    public function getCustomAdminComponentClass(): ?string
    {
        return $this->record->admin_component_class;
    }

    public function form(Form $form): Form
    {
        // Se houver campos definidos via JSON no admin_form_schema,
        // geramos os componentes do Filament dinamicamente.
        $schema = $this->buildSchemaFromJson($this->record->admin_form_schema ?? []);
        
        if (empty($schema)) {
            $schema = [
                Forms\Components\Section::make('Configurações Gerais')
                    ->description('Use esta área para configurar as propriedades fundamentais do plugin.')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('')
                            ->keyLabel('Atributo')
                            ->valueLabel('Valor')
                            ->addButtonLabel('+ Nova Configuração'),
                    ])
            ];
        }

        return $form->schema($schema);
    }

    protected function buildSchemaFromJson(array $jsonSchema): array
    {
        $components = [];
        
        foreach ($jsonSchema as $field) {
            $type = $field['type'] ?? 'text';
            $name = $field['name'] ?? null;
            $label = $field['label'] ?? ucfirst($name);
            
            if (!$name) continue;

            $component = match($type) {
                'toggle' => Forms\Components\Toggle::make($name)->label($label),
                'select' => Forms\Components\Select::make($name)->label($label)->options($field['options'] ?? []),
                'number' => Forms\Components\TextInput::make($name)->label($label)->numeric(),
                'textarea' => Forms\Components\Textarea::make($name)->label($label),
                default => Forms\Components\TextInput::make($name)->label($label),
            };

            if (isset($field['required']) && $field['required']) {
                $component->required();
            }

            $components[] = $component;
        }

        return $components;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendNotification = true): void
    {
        $data = $this->form->getState();
        
        $pluginSettings = PluginAdminSetting::updateOrCreate(
            ['plugin_id' => $this->record->id],
            ['settings' => $data]
        );

        Notification::make()->title('Configurações salvas com sucesso!')->success()->send();
    }
}
