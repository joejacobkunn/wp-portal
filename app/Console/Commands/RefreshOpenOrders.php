<?php

namespace App\Console\Commands;

use App\Models\Order\Order;
use App\Models\SX\Order as SXOrder;
use Illuminate\Console\Command;

class RefreshOpenOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sx:refresh-open-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to refresh local open orders with live sx orders and update local record';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $open_orders = Order::where('cono', 10)->whereIn('stage_code', [1,2])->get();

        foreach($open_orders as $open_order)
        {
            $sx_order = SXOrder::where('cono', 10)->where('orderno', $open_order->order_number)->where('ordersuf', $open_order->order_number_suffix)->first();

            if($sx_order->stagecd > 2)
            {
                if(in_array($sx_order->stagecd, [3,4,5])) $status = 'Closed';
                if(in_array($sx_order->stagecd, [9])) $status = 'Cancelled';

                $open_order->update([
                    'stage_code' => $sx_order->stagecd,
                    'status' => $status
                ]);
            }
            
        }

        echo "Updated ".count($open_orders).' local open orders from SX';
    }
}
