<?php

namespace App\Jobs;

use App\Exports\OrderExport;
use App\Models\Order\Order;
use App\Notifications\Orders\OrdersImportNotification;
use ArrayIterator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use League\Csv\Writer;
use Illuminate\Support\Str;

class ExportOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderType;

    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct($orderType, $user)
    {
        $this->orderType = $orderType;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ini_set('max_execution_time', 300);

        $stageCodes = $this->getStageCode();
        $path = $this->getValidRecords($stageCodes);

        Notification::send($this->user, new OrdersImportNotification($path, $this->orderType));

        $this->deleteFile($path);

    }

    public function getValidRecords($stageCodes)
    {
        if (!$stageCodes) {
            return [];
        }


        $fileName = uniqid() . '.csv';
        $filePath = storage_path('app/public/' . config('order.url') . $fileName);
        $writer = Writer::createFromPath($filePath, 'a+');
        $writer->forceEnclosure();
        $writer->encloseAll();

        $writer->insertOne([
            'id', 'cono', 'order_number', 'order_number_suffix', 'whse', 'taken_by',
            'is_dnr', 'order_date', 'promise_date', 'warehouse_transfer_available',
            'partial_warehouse_transfer_available', 'wt_transfers', 'is_web_order',
            'last_line_added_at', 'golf_parts', 'non_stock_line_items', 'is_sro',
            'last_followed_up_at', 'ship_via', 'line_items', 'is_sales_order', 'qty_ship',
            'qty_ord', 'stage_code', 'dnr_items', 'sx_customer_number', 'status', 'last_updated_by'
        ]);

        $query = Order::query();

        if($this->orderType == 'ready_for_shipment_web')
        {
            $query->openOrders()->orWhereColumn('qty_ord', '=', 'qty_ship')->where('is_web_order', 1);
        }

        if($this->orderType == 'open')
        {
            $query->openOrders();
        }

        if($this->orderType == 'closed')
        {
            $query->closedOrders();
        }


        $query
            ->chunk(1000, function ($orders) use ($writer) {

            $records = $orders->map(function ($order) {

                return [
                    'id' => $order->id,
                    'cono' => $order->cono,
                    'order_number' => $order->order_number,
                    'order_number_suffix' => $order->order_number_suffix,
                    'whse' => $order->whse,
                    'taken_by' => $order->taken_by,
                    'is_dnr' => $order->is_dnr,
                    'order_date' => $order->order_date,
                    'promise_date' => $order->promise_date,
                    'warehouse_transfer_available' => $order->warehouse_transfer_available,
                    'partial_warehouse_transfer_available' => $order->partial_warehouse_transfer_available,
                    'wt_transfers' => str_replace('"','`', json_encode($order->wt_transfers)),
                    'is_web_order' => $order->is_web_order,
                    'last_line_added_at' => $order->last_line_added_at,
                    'golf_parts' => str_replace('"','`', json_encode($order->golf_parts)),
                    'non_stock_line_items' => str_replace('"','`', json_encode($order->non_stock_line_items)),
                    'is_sro' => $order->is_sro,
                    'last_followed_up_at' => $order->last_followed_up_at,
                    'ship_via' => $order->ship_via,
                    'line_items' => str_replace('"','`', json_encode($order->line_items)),
                    'is_sales_order' => $order->is_sales_order,
                    'qty_ship' => $order->qty_ship,
                    'qty_ord' => $order->qty_ord,
                    'stage_code' => $order->stage_code,
                    'dnr_items' => str_replace('"','`', json_encode($order->dnr_items)),
                    'sx_customer_number' => $order->sx_customer_number,
                    'status' => $order->status->value,
                    'last_updated_by' => $order->last_updated_by,
                ];
            });
            $records->transform(function ($item) {
                $item['line_items'] = Str::limit($item['line_items'], 32000);
                return $item;
            });
            $writer->insertAll($records->toArray());
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
    private function deleteFile($fileName)
    {
        $filePath = config('order.url') . $fileName;
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
