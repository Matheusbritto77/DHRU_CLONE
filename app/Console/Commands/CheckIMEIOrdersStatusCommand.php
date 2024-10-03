<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ApiController;

class CheckIMEIOrdersStatusCommand extends Command
{
    protected $signature = 'orders:update';

    protected $description = 'Update IMEI orders status';

    public function handle()
    {
        $apiController = new ApiController();
         
        
        $apiController->server();
        $this->info('IMEI orders status updated successfully.');
    }
}
