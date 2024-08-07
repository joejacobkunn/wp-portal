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

    public $model;
    public $validData;
    public $errorRows = [];
    public $locations;
    /**
     * Create a new job instance.
     */
    public function __construct($validData, $model, $errorRows, $locations)
    {
        $this->validData = $validData;
        $this->model = $model;
        $this->errorRows = $errorRows;
        $this->locations = $locations;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->model->update(['status' => 'processing']);
        $this->setLocationId();
        $this->sendSms();

        $failedFile = $this->saveRecords(config('marketing.sms.failed_file_location'), $this->errorRows);
        $validFile = $this->saveRecords(config('marketing.sms.valid_file_location'), $this->validData);
        $this->model->update([
            'status' => 'complete',
            'failed_file' =>  $failedFile,
            'processed' =>  $validFile,
        ]);
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
           if ($kenet->send($row['phone'], $row['message'], $row['location_id']) == 'error') {
                $this->errorRows[] = $row;
                unset($this->validData[$key]);
           } else {
                $this->model->increment('processed_count');
           }
        }

        sleep(1);
    }
}
