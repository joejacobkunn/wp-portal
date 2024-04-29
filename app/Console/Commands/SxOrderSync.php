<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order\Order;
use App\Models\SX\Order as SXOrder;
use App\Models\SX\OrderLineItem;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;

class SxOrderSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:order-sync {--months=2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to sync orders from sx to local database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = date('Y-m-d', strtotime('-'.$this->option('months').' months'));
        $endDate = date('Y-m-d');
        $sx_orders = SXOrder::without(['customer'])->select(['cono', 'orderno','ordersuf','takenby', 'enterdt', 'stagecd', 'custno', 'user1', 'shipviaty', 'promisedt', 'stagecd', 'totqtyshp', 'totqtyord', 'whse', 'user6'])->where('cono', 10)->whereBetween('enterdt', [$startDate, $endDate])->get();

        foreach($sx_orders as $sx_order)
        {
            $line_items = $this->getSxOrderLineItemsProperty($sx_order['orderno'],$sx_order['ordersuf']);

            Order::updateOrCreate(            
                [
                    'order_number' => $sx_order['orderno'], 
                    'order_number_suffix' => $sx_order['ordersuf'], 
                    'cono' => $sx_order['cono']
                ],
                [
                    'whse' => strtolower($sx_order['whse']),
                    'taken_by' => $sx_order['takenby'],
                    'order_date' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                    'stage_code' => $sx_order['stagecd'],
                    'sx_customer_number' => $sx_order['custno'],
                    'is_sro' => $sx_order['user1'] == 'SRO' ? 1 : 0,
                    'ship_via' => $sx_order['shipviaty'],
                    'qty_ship' => $sx_order['totqtyshp'],
                    'qty_ord' => $sx_order['totqtyord'],
                    'promise_date' => $sx_order['promisedt'],
                    'line_items' => ['line_items' => $line_items->toArray() ?: []],
                    'is_sales_order' => $this->isSales($line_items->toArray()),
                    'warehouse_transfer_available' => $this->checkForWarehouseTransfer($sx_order,$line_items),
                    'is_web_order' => $sx_order['user6'] == '6' ? 1 : 0,
                    'status' => $this->status($sx_order['stagecd'])
                ]
            );
        }

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



    private function status($stage_code)
    {
        if(in_array($stage_code, [3,4,5])) return 'Closed';
        if(in_array($stage_code, [9])) return 'Cancelled';
        return 'Pending Review';
    }

    private function isSales($line_items)
    {
        foreach($line_items as $line_item)
        {
            if(str_contains($line_item['prodline'], '-E')) return true;
        }

        return false;
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

}
