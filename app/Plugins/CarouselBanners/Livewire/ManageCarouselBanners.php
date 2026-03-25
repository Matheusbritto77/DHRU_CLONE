<?php

namespace App\Plugins\CarouselBanners\Livewire;

use App\Models\Plugin;
use App\Models\PluginAdminSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Filament\Notifications\Notification;

class ManageCarouselBanners extends Component implements HasForms
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
                Forms\Components\Section::make('Imagens do Carrossel')
                    ->description('Selecione as fotos que serão alternadas no banner principal.')
                    ->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Banners')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->directory('plugin_uploads/carousel-banners')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Configurações de Exibição')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('interval_ms')
                                ->label('Velocidade de Transição (ms)')
                                ->numeric()
                                ->default(4000),
                            Forms\Components\ColorPicker::make('bg_color')
                                ->label('Cor de Fundo da Seção')
                                ->default('#f5f5f7'),
                        ]),
                    ]),
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
            ->title('Banners salvos com sucesso!')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('plugins.carousel-banners.livewire.manage-carousel-banners');
    }
}
