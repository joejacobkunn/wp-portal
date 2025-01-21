<?php

namespace App\Jobs;

use App\Notifications\Scheduler\TruckScheduleImportNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class ProcessTruckScheduleImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $records;
    public $truck;
    public $user;
    public $failedRecords = [];
    /**
     * Create a new job instance.
     */
    public function __construct($truck, $records, $faild, $user)
    {
        $this->records = $records;
        $this->truck = $truck;
        $this->failedRecords = $faild;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->records as $record) {
            $timeslots = explode('-', $record['timeslots']);
            $this->truck->schedules()->updateOrCreate(
                [
                    'schedule_date' => Carbon::parse($record['date'])->format('Y-m-d'),
                    'zone_id' => trim($record['zone_id']),
                ],
                [
                    'start_time' => trim($timeslots[0]),
                    'end_time' => trim($timeslots[1]),
                    'slots' => trim($record['slots']),
                ]
            );
        }
        Notification::send($this->user, new TruckScheduleImportNotification());
    }
}
