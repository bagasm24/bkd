<?php

namespace App\Console\Commands;

use App\Http\Controllers\SdmController;
use Illuminate\Console\Command;

class SyncDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data SDM dari API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $sdmController = new SdmController();
            $sdmController->getAllDataSDM();
            $this->info('Sinkronisasi data SDM selesai.');
        } catch (\Exception $e) {
            $this->error('Gagal sinkronisasi: ' . $e->getMessage());
        }
    }
}
