<?php

namespace App\Console\Commands;

use App\Support\Payments\PaymentGatewayManager;
use Illuminate\Console\Command;

class ReconcilePaymentGatewaysCommand extends Command
{
    protected $signature = 'payment:reconcile-gateways {gateway?}';

    protected $description = 'Reconcilia pagamentos pendentes dos gateways ativos.';

    public function handle(PaymentGatewayManager $manager): int
    {
        $results = $manager->reconcile($this->argument('gateway'));

        foreach ($results as $gateway => $result) {
            $this->line(sprintf(
                '%s => checked=%d updated=%d',
                $gateway,
                (int) ($result['checked'] ?? 0),
                (int) ($result['updated'] ?? 0),
            ));
        }

        return self::SUCCESS;
    }
}
