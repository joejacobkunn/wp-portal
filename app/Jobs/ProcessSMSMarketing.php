<?php

namespace App\Jobs;

use App\Exports\SMSMarketingExport;
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
    /**
     * Create a new job instance.
     */
    public function __construct($validData, $model, $errorRows)
    {
        $this->validData = $validData;
        $this->model = $model;
        $this->errorRows = $errorRows;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (config('sx.mock')) {
            foreach($this->validData as $row) {
                $value = mt_rand(0,1);
                if ($value) {
                    $this->model->increment('processed_count');
                } else {
                    $this->errorRows[] = $row;
                }
            }
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
}
