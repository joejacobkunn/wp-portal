<?php

namespace App\Jobs;

use App\Exports\OrderExport;
use App\Models\Order\Order;
use App\Notifications\Orders\OrdersImportNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ProcessOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $importFile;
    public $importErrorRows;
    public $orderType;
    /**
     * Create a new job instance.
     */
    public function __construct($importFile, $errorFile, $orderType)
    {
        $this->importFile = $importFile;
        $this->importErrorRows = $errorFile;
        $this->orderType = $orderType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $orders = $this->importFile;
        if($this->orderType != 'all') {

            $stageCodes = $this->getStageCode();
            $orders = $this->getValidRecords($stageCodes);
        }

        foreach ($orders as $order) {
            order::create($order);
        }
        $path = $this->storeFailedRecords();

        $total = count($this->importErrorRows)+ count($orders);
        $user = Auth::user();
        Notification::send($user, new OrdersImportNotification($total, count($this->importErrorRows), $path));
    }

    public function getValidRecords($stageCodes)
    {
        if (!$stageCodes) {
            return [];
        }

        foreach($this->importFile as $key => $order) {
            if(in_array($order['stage_code'], $stageCodes)) {
                $filteredRecords[] = $order;
            } else {
                $this->importErrorRows[] = $order;
            }
        }
        return $filteredRecords;
    }

    public function getStageCode()
    {
        switch ($this->orderType) {
            case 'open':
                    $stageCode =  [1,2];
                    break;
            case 'closed':
                $stageCode =  [3,4,5];
                break;
            case 'cancelled':
                $stageCode =  [9];
                break;
            case 'quotes':
                $stageCode =  [0];
                break;
            default:
            $stageCode =  false;
        }
        return $stageCode;

    }

    public function storeFailedRecords()
    {
        $recordPath =  config('order.url'). uniqid() . '.csv';
        if (!empty($this->importErrorRows)) {
            $exportRecord = new OrderExport($this->importErrorRows);
            Excel::store($exportRecord,  $recordPath, 'public');
            return $recordPath;
        }
        return null;
    }
}
