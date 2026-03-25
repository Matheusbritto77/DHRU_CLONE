<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class CurrencySyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:sync-currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize exchange rates for all active currencies with external API.';

    /**
     * Execute the console command.
     */
    public function handle(CurrencyService $currencyService)
    {
        $this->info('Starting currency synchronization...');
        
        $currencyService->syncAll();
        
        $this->info('Synchronization completed successfully.');
    }
}
