<?php

namespace App\Console\Commands;

use App\Models\Order\Order;
use App\Models\SX\Order as SXOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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
            $sx_order = SXOrder::select('user1','stagecd','shipviaty','totqtyshp','totqtyord','promisedt')->where('cono',$open_order->cono)->where('orderno', $open_order->order_number)->where('ordersuf', $open_order->order_number_suffix)->first();
            $status = $open_order->status;

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
                'promise_date' => Carbon::parse($sx_order['promisedt'])->format('Y-m-d'),
            ]);

        }

        //store last time in Cache

        Cache::put('order_data_sync_timestamp', now());

    }
}
