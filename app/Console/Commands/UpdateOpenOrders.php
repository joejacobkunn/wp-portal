<?php

namespace App\Console\Commands;

use App\Models\Order\Order;
use App\Models\SX\Order as SXOrder;
use Illuminate\Console\Command;

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
            $sx_order = SXOrder::select('stagecd')->where('orderno', $open_order->order_number)->where('ordersuf', $open_order->order_number_suffix)->first();

            if($sx_order->stagecd != $open_order->stage_code){
                $updated_count++;
                if(in_array($sx_order->stagecd, [3,4,5])) $status = 'Closed';
                if(in_array($sx_order->stagecd, [9])) $status = 'Cancelled';
                $open_order->update(['status' => $status, 'stage_code' => $sx_order->stagecd]);
            }
        }

        echo "Updated ".$updated_count." from ".count($open_orders)." open orders";
    }
}
