<?php

namespace App\Jobs;

use App\Exports\SMSMarketingExport;
use App\Services\Kenect;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProcessSMSMarketing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $model;
    public $validData;
    public $errorRows = [];
    public $locations;
    public $teams = [];
    /**
     * Create a new job instance.
     */
    public function __construct($validData, $model, $errorRows, $locations, $teams)
    {
        $this->validData = $validData;
        $this->model = $model;
        $this->errorRows = $errorRows;
        $this->locations = $locations;
        $this->teams = $teams;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if($this->model->processed_count == 0 || empty($this->model->processed_count))
        {
            $this->model->update(['status' => 'processing']);
            $this->setLocationId();
            $this->setAssigneeId();
            $this->sendSms();
            $failedFile = $this->saveRecords(config('marketing.sms.failed_file_location'), $this->errorRows);
            $validFile = $this->saveRecords(config('marketing.sms.valid_file_location'), $this->validData);
            $this->model->update([
                'status' => 'complete',
                'failed_file' =>  $failedFile,
                'processed' =>  $validFile,
            ]);
    
        }
        else{
            $this->model->update(['status' => 'complete']);
        }
    }

    public function saveRecords($path, $records)
    {
        $recordPath =  $path. uniqid() . '.csv';
        if (!empty($records)) {
            $exportRecord = new SMSMarketingExport($records);
            Excel::store($exportRecord,  $recordPath, 'public');
            return $recordPath;
        }
        return null;
    }

    public function setLocationId()
    {
        foreach($this->validData as $key => $row) {
                foreach ($this->locations as $location) {
                    if (str_contains(strtolower(trim($location->name)),strtolower(trim($row['office'])))) {
                        $this->validData[$key]['location_id'] = $location->id;
                        break;
                    }
                }
        }
    }

    public function sendSms()
    {
        $kenet = new Kenect();
        foreach ($this->validData as $key => $row) {
           if ($kenet->send($row['phone'], $row['message'], $row['location_id'], $row['teamId']) == 'error') {
                $this->errorRows[] = $row;
                unset($this->validData[$key]);
           } else {
                $this->model->increment('processed_count');
           }
        }

        sleep(0.5);
    }

    public function setAssigneeId()
    {

        foreach($this->validData as $key => $row) {
            foreach ($this->teams as $team) {
                if (str_contains(strtolower(trim($team->name)), strtolower(trim($row['assignee'])))) {
                    $this->validData[$key]['teamId'] = $team->id;
                    break;
                }
            }
    }
    }
}
