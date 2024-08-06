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
        if (config('sx.mock')) {
            $this->setLocationId();
            $this->sendSms();
        } else {
            //
        }

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
            $value = mt_rand(0,1);
            if ($value) {
                foreach ($this->locations as $location) {
                    if (str_replace('Weingartz - ', '', $location->name) === trim($row['office'])) {
                        $this->validData[$key]['location_id'] = $location->id;
                        break;
                    }
                }
            } else {
                $this->errorRows[] = $row;
                unset($this->validData[$key]);
            }
        }
    }

    public function sendSms()
    {
        $kenet = new Kenect();
        foreach ($this->validData as $key => $row) {
           if ($kenet->send($row['phone'], $row['message']) === 'error') {
                $this->errorRows[] = $row;
                unset($this->validData[$key]);
           } else {
                $this->model->increment('processed_count');
           }
        }
    }
}
