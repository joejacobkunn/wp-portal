<?php

namespace App\Console\Commands;

use App\Models\Order\Order;
use App\Models\SX\Order as SXOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\SX\OrderLineItem;
use Illuminate\Database\Query\JoinClause;


class UpdateOpenOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:update-open-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update open orders in MySQL with SX';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updated_count = 0;

        //query open orders from mysql

        $open_orders = Order::openOrders()->get();

        foreach($open_orders as $open_order)
        {
            //fetch sx order
            $sx_order = SXOrder::select('user1','stagecd','shipviaty','totqtyshp','totqtyord','promisedt','whse')->where('cono',$open_order->cono)->where('orderno', $open_order->order_number)->where('ordersuf', $open_order->order_number_suffix)->first();
            $status = $open_order->status;
            $line_items = $this->getSxOrderLineItemsProperty($open_order->order_number,$open_order->order_number_suffix);

            $updated_count++;
            if(in_array($sx_order->stagecd, [3,4,5])) $status = 'Closed';
            if(in_array($sx_order->stagecd, [9])) $status = 'Cancelled';

            $open_order->update([
                'status' => $status, 
                'stage_code' => $sx_order->stagecd,
                'is_sro' => $sx_order['user1'] == 'SRO' ? 1 : 0,
                'ship_via' => $sx_order['shipviaty'],
                'qty_ship' => $sx_order['totqtyshp'],
                'qty_ord' => $sx_order['totqtyord'],
                'line_items' => ['line_items' => $line_items->toArray() ?: []],
                'is_sales_order' => $this->isSales($line_items->toArray()),
                'warehouse_transfer_available' => $this->checkForWarehouseTransfer($sx_order,$line_items),
                'promise_date' => Carbon::parse($sx_order['promisedt'])->format('Y-m-d'),
            ]);

        }

        //store last time in Cache

        Cache::put('order_data_sync_timestamp', now());

    }

    private function checkForWarehouseTransfer($sx_order, $line_items)
    {
        if($sx_order->isBackOrder())
        {
            foreach($line_items as $line_item)
            {
                $backorder_count = intval($line_item->stkqtyord) - intval($line_item->stkqtyship);

                if($backorder_count > 0)
                {
                    $inventory_levels = $line_item->checkInventoryLevelsInWarehouses(array_diff(['ann','ceda','farm','livo','utic','wate', 'zwhs'], [strtolower($line_item->whse)]));

                    foreach($inventory_levels as $inventory_level)
                    {
                        $available_stock = $inventory_level->qtyonhand - ($inventory_level->qtycommit + $inventory_level->qtyreservd);

                        if($available_stock >= $backorder_count) return true;
                    }
                }
            }
        }

        return false;

    }

    private function getSxOrderLineItemsProperty($order_number, $order_suffix, $cono = 10)
    {
        $required_line_item_columns = [
            'oeel.orderno',
            'oeel.ordersuf',
            'oeel.shipto',
            'oeel.lineno',
            'oeel.qtyord',
            'oeel.proddesc',
            'oeel.price',
            'oeel.shipprod',
            'oeel.statustype',
            'oeel.prodcat',
            'oeel.prodline',
            'oeel.specnstype',
            'oeel.qtyship',
            'oeel.ordertype',
            'oeel.netamt',
            'oeel.orderaltno',
            'oeel.user8',
            'oeel.vendno',
            'oeel.whse',
            'oeel.stkqtyord',
            'oeel.stkqtyship',
            'oeel.returnfl',
            'icsp.descrip',
            'icsl.user3',
            'icsl.whse',
            'icsl.prodline',
            'oeel.cono',
        ];
    
        return OrderLineItem::select($required_line_item_columns)
        ->leftJoin('icsp', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsp.cono')
            ->on('oeel.shipprod', '=', 'icsp.prod');
                //->where('icsp.cono', $this->customer->account->sx_company_number);
        })
        ->leftJoin('icsl', function (JoinClause $join) {
            $join->on('oeel.cono','=','icsl.cono')
                ->on('oeel.whse', '=', 'icsl.whse')
                ->on('oeel.vendno', '=', 'icsl.vendno')
                ->on('oeel.prodline', '=', 'icsl.prodline');
        })
        ->where('oeel.orderno', $order_number)->where('oeel.ordersuf', $order_suffix)
        ->where('oeel.cono', $cono)
        ->orderBy('oeel.lineno', 'asc')
        ->get();
    }


}
