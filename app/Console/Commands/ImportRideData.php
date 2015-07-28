<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use App\Http\Controllers\RideController;

class ImportRideData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importRideData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports the ride data from the API';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        // Create the controller to call the action from and call it
		$rideController = new RideController();
		$rideController->fetchRideData();
    }
}
