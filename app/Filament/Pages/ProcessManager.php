<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Exception;

class ProcessManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'Suporte';
    protected static ?string $navigationLabel = 'Gerenciador de Processos';
    protected static ?string $title = 'Gerenciador de Processos (BUN)';
    protected static string $view = 'filament.pages.process-manager';

    public array $processes = [];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        try {
            $response = Http::timeout(2)->get('http://localhost:8080/api/status');
            if ($response->successful()) {
                $this->processes = $response->json('processes', []);
            } else {
                Notification::make()
                    ->title('Erro ao conectar ao Servidor Bun')
                    ->warning()
                    ->send();
                $this->processes = [];
            }
        } catch (Exception $e) {
            $this->processes = [];
        }
    }

    public function callAction(string $name, string $action)
    {
        try {
            $response = Http::post('http://localhost:8080/api/control', [
                'name' => $name,
                'action' => $action,
            ]);

            if ($response->successful()) {
                Notification::make()
                    ->title('Comando enviado com sucesso')
                    ->success()
                    ->send();
                
                $this->refreshData();
            } else {
                Notification::make()
                    ->title('Erro ao processar comando')
                    ->danger()
                    ->send();
            }
        } catch (Exception $e) {
             Notification::make()
                ->title('Servidor de Gerenciamento Offline')
                ->danger()
                ->send();
        }
    }

    public function start(string $name) => $this->callAction($name, 'start');
    public function stop(string $name) => $this->callAction($name, 'stop');
    public function restart(string $name) => $this->callAction($name, 'restart');
}
