<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockPick;

class UpdateStockPrices extends Command
{
    protected $signature = 'stockpicks:update';
    protected $description = 'Update current prices for all stock picks';

    public function handle()
    {
        $this->info('Price update functionality will be implemented when financial API is connected');
        $this->info('For now, use the API endpoint to update prices manually');
    }
}
