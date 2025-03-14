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
        if($this->truck->service_type->value == 'pickup_delivery') {
            $this->updateDeliveryPickupSchedules();
        }
        if($this->truck->service_type->value == 'at_home_maintenance') {
            $this->updateAHMSchedules();
        }

        Notification::send($this->user, new TruckScheduleImportNotification());
    }

    public function updateDeliveryPickupSchedules()
    {
        foreach($this->records as $record) {
            $timeslots = explode('-', trim($record['timeslots']));
            $starTime = date("h:i A", strtotime($timeslots[0]));
            $endTime = date("h:i A", strtotime($timeslots[1]));
            $this->truck->schedules()->updateOrCreate(
                [
                    'schedule_date' => Carbon::parse($record['date'])->format('Y-m-d'),
                    'start_time' => $starTime,
                    'end_time' => $endTime
                ],
                [
                    'zone_id' => trim($record['zone_id']),
                    'slots' => trim($record['slots']),
                    'is_pickup' => trim($record['is_pickup']),
                    'is_delivery' => trim($record['is_delivery']),
                ]
            );
        }
    }

    public function updateAHMSchedules()
    {
        foreach($this->records as $record) {
            $timeslots = explode('-', trim($record['timeslots']));
            $starTime = date("h:i A", strtotime($timeslots[0]));
            $endTime = date("h:i A", strtotime($timeslots[1]));
            $this->truck->schedules()->updateOrCreate(
                [
                    'schedule_date' => Carbon::parse($record['date'])->format('Y-m-d'),
                    'start_time' => $starTime,
                    'end_time' => $endTime
                ],
                [
                    'zone_id' => trim($record['zone_id']),
                    'slots' => trim($record['slots']),
                    'is_pickup' => 0,
                    'is_delivery' => 0,
                ]
            );
        }
    }
}
