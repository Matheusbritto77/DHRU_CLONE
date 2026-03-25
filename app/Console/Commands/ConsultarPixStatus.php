<?php

namespace App\Console\Commands;

use App\Support\Payments\PaymentGatewayManager;
use Illuminate\Console\Command;

class ConsultarPixStatus extends Command
{
    protected $signature = 'pix:consultar-status';
    protected $description = 'Consulta o status dos pagamentos PIX pendentes conforme a configuracao do plugin';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(PaymentGatewayManager $manager)
    {
        $result = $manager->reconcile('payment-gerencianet-pix')['payment-gerencianet-pix'] ?? ['checked' => 0, 'updated' => 0];
        $this->info("Gerencianet PIX reconciliado via plugin. checked={$result['checked']} updated={$result['updated']}");

        return self::SUCCESS;
    }
}
