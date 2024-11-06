<?php

namespace App\Jobs;

use App\Exports\WarrantyReportExport;
use App\Models\Equipment\Warranty\Report;
use App\Notifications\Warranty\WarrantyExportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use League\Csv\Writer;
use Maatwebsite\Excel\Facades\Excel;

class ExportWarrantyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fileName = uniqid() . '.csv';
        $filePath = storage_path('app/public/' . config('warranty.record_path') . $fileName);
        $writer = Writer::createFromPath($filePath, 'a+');
        $writer->forceEnclosure();
        $writer->encloseAll();
        $writer->insertOne([
            'id',
            'store',
            'order_no',
            'shiptoname',
            'address',
            'address2',
            'city',
            'state',
            'zip',
            'brand',
            'prodline',
            'model',
            'serial',
            'description',
            'sold',
            'registration_date',
            'registered_by',
            'cust_no',
            'cust_type',
            'customer_name'
        ]);
        $query = Report::query();
        $query
        ->chunk(1000, function ($orders) use ($writer) {
            $writer->insertAll($orders->toArray());
        });
        Notification::send($this->user, new WarrantyExportNotification($fileName));

    }

}
