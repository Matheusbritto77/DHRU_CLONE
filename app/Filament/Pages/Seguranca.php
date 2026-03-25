<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class Seguranca extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $title = 'Segurança do Sistema';

    protected static string $view = 'filament.pages.seguranca';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'admin_path' => env('FILAMENT_ADMIN_PATH', 'admin'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('URL de Acesso')
                    ->description('Proteja seu painel administrativo mudando o endereço padrão.')
                    ->schema([
                        TextInput::make('admin_path')
                            ->label('URL Secreta do Painel Admin')
                            ->helperText('O padrão atual é "admin". Se mudar, será deslogado e deverá acessar através do novo link (ex: sua-loja.com/novo-nome-oculto).')
                            ->required()
                            ->prefix(url('/') . '/'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $currentAdminPath = env('FILAMENT_ADMIN_PATH', 'admin');
        
        $this->updateEnv([
            'FILAMENT_ADMIN_PATH' => trim($data['admin_path']),
        ]);

        Notification::make()
            ->title('Segurança Atualizada!')
            ->success()
            ->send();

        if ($data['admin_path'] !== $currentAdminPath) {
             $this->redirect('/' . trim($data['admin_path']) . '/seguranca');
        }
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
