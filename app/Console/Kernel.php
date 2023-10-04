<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\WebhookClient\Models\WebhookCall;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        //rerun failed jobs every 3 hours
        $schedule->command('queue:retry all')->everyThreeHours();

        //task to sync customer last sale dates from sx to local database
        $schedule->command('sx:last-sale-date-sync')->timezone('America/New_York')->dailyAt('21:15');

        //task to refresh open order data
        $schedule->command('import:sx customer-order-status-sync weingartz')->timezone('America/New_York')->dailyAt('02:15');

        //purge old auth logs that are more than a year old
        $schedule->command('authentication-log:purge')->monthly();

        //purge old webhooks daily - 90 days or older
        $schedule->command('model:prune', [
            '--model' => [WebhookCall::class],
        ])->daily();

        //purge telescope entries
        $schedule->command('telescope:prune --hours=48')->daily();

        $schedule->command('media-library:delete-old-temporary-uploads')->daily();

        //cron to clean up laravel log files
        $schedule->command('logcleaner:run', ['--keeplines' => 5000, '--keepfiles' => 14])->daily()->timezone('America/New_York')->at('06:00');

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
