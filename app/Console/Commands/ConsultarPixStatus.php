<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\GerencianetPixController;

class ConsultarPixStatus extends Command
{
    protected $signature = 'pix:consultar-status';
    protected $description = 'Consulta o status dos PIX recebidos nos últimos 30 minutos';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Instancia o controlador
        $controller = new GerencianetPixController();

        // Chama o método diretamente
        $response = $controller->consultarPixRecebidosUltimos30Minutos();

        // Verifica se a resposta foi bem-sucedida
        if ($response->getStatusCode() == 200) {
            $this->info('Status dos PIX consultado com sucesso.');
        } else {
            $this->error('Erro ao consultar o status dos PIX.');
        }

        return 0;
    }
}
