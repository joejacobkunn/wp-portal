<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //task to sync customer last sale dates from sx to local database
        $schedule->command('sx:last-sale-date-sync')->timezone('America/New_York')->at('21:30');

        //task to refresh open order data
        $schedule->command('import:sx customer-order-status-sync weingartz')->timezone('America/New_York')->at('21:15');

        $schedule->command('media-library:delete-old-temporary-uploads')->daily();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
