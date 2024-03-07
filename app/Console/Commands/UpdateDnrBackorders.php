<?php

namespace App\Console\Commands;

use App\Models\Order\DnrBackorder;
use App\Models\Order\Order as PortalOrder;
use App\Models\SX\Order;
use App\Models\SX\OrderLineItem;
use App\Models\SX\Warehouse;
use App\Models\SX\WarehouseProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateDnrBackorders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:update-dnr-backorders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update DNR Backorders in local mysql database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dnr_count = 0;

        //Get all weingartz warehouses
        $warehouses = Warehouse::select('whse')->where('cono', 10)->where('salesfl',1)->where('custno',0)->get();

        //Loop thru each warehouse and fetch all orders where stagecd <=2 and transtype <> 'QU'

        foreach($warehouses as $warehouse){
            $orders = Order::select('cono', 'orderno', 'ordersuf', 'enterdt', 'custno', 'stagecd')->where('cono', 10)->where('whse',$warehouse->whse)->whereIn('stagecd', [1,2])->where('takenby', 'WEB')->whereNot('transtype','QU')->get();
            
            //loop thru each orders line item

            foreach($orders as $order){
                
                $dnr_items = [];

                $line_items = OrderLineItem::select('orderno','ordersuf','statustype','qtyord', 'qtyship', 'qtyrel', 'shipprod')->where('cono', 10)->where('orderno', $order->orderno)->where('ordersuf', $order->ordersuf)->where('statustype',"A")->whereColumn('qtyord', '<>', DB::raw('(qtyship + qtyrel)'))->get();

                //loop thru each line items
                foreach($line_items as $line_item){

                    $dnr_warehouse_product = WarehouseProduct::where('cono',10)->where('whse', $warehouse->whse)->where('prod', $line_item->shipprod)->where('statustype',"X")->get();
                    
                    if($dnr_warehouse_product->isNotEmpty()) {
                        $dnr_items [] = $line_item->shipprod;
                    }
                }

                //if atleast one dnr item, flag as dnr

                if(!empty($dnr_items))
                {
                    $dnr_count++;
                    
                    PortalOrder::where('order_number',$order->orderno)->where('order_number_suffix',$order->ordersuf)->update(['is_dnr' => 1,'dnr_items' =>  $dnr_items ,'status' => 'Pending Review']);
                    
                }

            }
        }

        echo "Flagged ".$dnr_count." orders as DNR";

    }
}
