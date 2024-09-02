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

    public $orderType;
    /**
     * Create a new job instance.
     */
    public function __construct($orderType)
    {
        $this->orderType = $orderType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ini_set('max_execution_time', 300);

        $stageCodes = $this->getStageCode();
        $path = $this->getValidRecords($stageCodes);

        $user = Auth::user();
        Notification::send($user, new OrdersImportNotification($path));
    }

    public function getValidRecords($stageCodes)
    {
        if (!$stageCodes) {
            return [];
        }

        $fileName = uniqid().'.csv';
        Order::whereIn('stage_code', $stageCodes)->chunk(1000, function ($orders) use ($fileName) {
            $filePath = storage_path('app/public/'.config('order.url') . $fileName);

            $fileExists = file_exists($filePath);

            $fileHandle = fopen($filePath, 'a+');


            if (!$fileExists) {
                fputcsv($fileHandle, [
                    'id', 'cono', 'order_number', 'order_number_suffix', 'whse', 'taken_by',
                    'is_dnr', 'order_date', 'promise_date', 'warehouse_transfer_available',
                    'partial_warehouse_transfer_available', 'wt_transfers', 'is_web_order',
                    'last_line_added_at', 'golf_parts', 'non_stock_line_items', 'is_sro',
                    'last_followed_up_at', 'ship_via', 'line_items', 'is_sales_order', 'qty_ship',
                    'qty_ord', 'stage_code', 'dnr_items', 'sx_customer_number', 'status', 'last_updated_by'
                ]);
            }

            foreach ($orders as $order) {

                fputcsv($fileHandle, [
                    $order->id, $order->cono, $order->order_number, $order->order_number_suffix,
                    $order->whse, $order->taken_by, $order->is_dnr, $order->order_date,
                    $order->promise_date, $order->warehouse_transfer_available,
                    $order->partial_warehouse_transfer_available,
                    json_encode($order->wt_transfers),
                    $order->is_web_order, $order->last_line_added_at, json_encode($order->golf_parts, JSON_UNESCAPED_UNICODE),
                    json_encode($order->non_stock_line_items, JSON_UNESCAPED_UNICODE), $order->is_sro, $order->last_followed_up_at, $order->ship_via,
                    json_encode($order->line_items, JSON_UNESCAPED_UNICODE), $order->is_sales_order, $order->qty_ship, $order->qty_ord, $order->stage_code,
                    json_encode($order->dnr_items, JSON_UNESCAPED_UNICODE), $order->sx_customer_number, $order->status->value, $order->last_updated_by
                ]);
            }

            fclose($fileHandle);
        });
        return $fileName;

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

}
