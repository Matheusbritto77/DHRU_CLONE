<?php

namespace App\Filament\Pages;

use App\Models\Plugin;
use App\Support\PluginAdminManager;
use App\Support\PluginAdminSchemaFactory;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class Personalizacao extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $title = 'Personalização do Site';

    protected static string $view = 'filament.pages.personalizacao';

    public ?array $data = [];

    public function mount(): void
    {
        $pluginAdmin = Plugin::query()
            ->where('has_admin_panel', true)
            ->whereIn('admin_panel_location', ['site_customization', 'both'])
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Plugin $plugin) => [
                $plugin->slug => PluginAdminManager::getFormData($plugin),
            ])
            ->all();

        $this->form->fill([
            'site_name' => config('app.name'),
            'plugin_admin' => $pluginAdmin,
        ]);
    }

    public function form(Form $form): Form
    {
        $pluginTabs = Plugin::query()
            ->where('has_admin_panel', true)
            ->whereIn('admin_panel_location', ['site_customization', 'both'])
            ->orderBy('name')
            ->get()
            ->map(function (Plugin $plugin) {
                $tab = Tabs\Tab::make($plugin->getAdminNavigationLabel())
                    ->icon($plugin->admin_navigation_icon ?: 'heroicon-o-puzzle-piece');

                if ($plugin->admin_component_class && class_exists($plugin->admin_component_class)) {
                    return $tab->schema([
                        Livewire::make($plugin->admin_component_class, ['plugin' => $plugin])
                            ->key("plugin-tab-{$plugin->slug}"),
                    ]);
                }

                return $tab->schema(
                    PluginAdminSchemaFactory::build(
                        $plugin->admin_form_schema ?? [],
                        "plugin_admin.{$plugin->slug}"
                    )
                );
            })
            ->all();

        return $form
            ->schema([
                Tabs::make('CustomizationTabs')
                    ->tabs(array_merge([
                        Tabs\Tab::make('Identidade do Site')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Nome Principal do Site')
                                    ->required(),
                                FileUpload::make('logo')
                                    ->label('Logo do Site (Apenas arquivos PNG ou JPG)')
                                    ->image()
                                    ->directory('temp_uploads')
                                    ->helperText('Fazer o upload enviará automaticamente o logotipo substituindo o logotipo em modo claro e escuro globalmente.'),
                            ]),
                    ], $pluginTabs))->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $pluginAdminData = $data['plugin_admin'] ?? [];

        $publicPath = public_path();

        // Migração de Logo
        if (filled($data['logo'])) {
            $path = storage_path('app/public/' . $data['logo']);
            if (File::exists($path)) {
                File::copy($path, $publicPath . '/logo.png');
                File::copy($path, $publicPath . '/logo3.jpg'); // Retrocompatibilidade (Blade antigo)
            }
        }

        // Atualização do ENV (Nome Oficial)
        
        $this->updateEnv([
            'APP_NAME' => '"' . trim($data['site_name']) . '"',
        ]);

        Plugin::query()
            ->where('has_admin_panel', true)
            ->whereIn('admin_panel_location', ['site_customization', 'both'])
            ->get()
            ->each(function (Plugin $plugin) use ($pluginAdminData): void {
                PluginAdminManager::save($plugin, $pluginAdminData[$plugin->slug] ?? []);
            });

        Notification::make()
            ->title('Personalizações Atualizadas!')
            ->success()
            ->send();

        // Reseta cache dos uploads
        $this->form->fill([
            'site_name' => $data['site_name'],
            'logo' => null,
            'plugin_admin' => Plugin::query()
                ->where('has_admin_panel', true)
                ->whereIn('admin_panel_location', ['site_customization', 'both'])
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn (Plugin $plugin) => [
                    $plugin->slug => PluginAdminManager::getFormData($plugin),
                ])
                ->all(),
        ]);
    }

    private function updateEnv($data = [])
    {
        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $env = File::get($envPath);
            foreach ($data as $key => $value) {
                if (preg_match("/^{$key}=/m", $env)) {
                    $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);
                } else {
                    $env .= "\n{$key}={$value}";
                }
            }
            File::put($envPath, $env);
            Artisan::call('optimize:clear');
        }
    }
}
