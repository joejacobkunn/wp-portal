<?php

namespace Database\Seeders;

use App\Models\Order\Order;
use App\Models\SX\Order as SXOrder;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = date('Y-m-d', strtotime('-3 months'));
        $endDate = date('Y-m-d');
        $sx_orders = SXOrder::where('cono', 10)->whereBetween('enterdt', [$startDate, $endDate])->get();

        foreach($sx_orders as $sx_order)
        {
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
                    'qty_ship' => $sx_order['qtyship'],
                    'qty_ord' => $sx_order['qty_ord'],
                    'promise_date' => $sx_order['promisedt'],
                    'status' => $this->status($sx_order['stagecd'])
                ]
            );
        }
    }

    private function status($stage_code)
    {
        if(in_array($stage_code, [3,4,5])) return 'Closed';
        if(in_array($stage_code, [9])) return 'Cancelled';
        return 'Pending Review';
    }
}
