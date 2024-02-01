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
        $schedule->command('queue:prune-failed --hours=72')->dailyAt('07:45');

        //task to sync customer last sale dates from sx to local database
        $schedule->command('sx:last-sale-date-sync')->timezone('America/New_York')->dailyAt('21:15');

        //task to fetch dnr orders
        $schedule->command('sx:fetch-dnr-backorders')->timezone('America/New_York')->dailyAt('20:15');

        //task to refresh open order data
        $schedule->command('import:sx customer-order-status-sync weingartz')->timezone('America/New_York')->dailyAt('03:15');

        //purge old auth logs that are more than a year old
        $schedule->command('authentication-log:purge')->monthly();

        //purge old webhooks daily - 90 days or older
        $schedule->command('model:prune', [
            '--model' => [WebhookCall::class],
        ])->dailyAt('04:45');

        //purge telescope entries
        $schedule->command('telescope:prune --hours=48')->dailyAt('07:30');

        $schedule->command('media-library:delete-old-temporary-uploads')->dailyAt('06:30');

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
