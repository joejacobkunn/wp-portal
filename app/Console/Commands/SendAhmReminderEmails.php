<?php

namespace App\Console\Commands;

use App\Events\Scheduler\EventReminder;
use App\Models\Scheduler\Schedule;
use Illuminate\Console\Command;

class SendAhmReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-ahm-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder emails for AHM appointments 48 hours prior';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schedules = Schedule::where('schedule_date', today()->addDay(2)->format('Y-m-d'))->where('status', 'confirmed')->get();

        foreach($schedules as $schedule)
        {
            EventReminder::dispatch($schedule);
        }
    }
}
