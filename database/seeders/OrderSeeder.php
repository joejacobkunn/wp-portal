<?php

namespace Database\Seeders;

use App\Models\Order\Order;
use App\Models\SX\Order as SXOrder;
use App\Models\SX\OrderLineItem;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Seeder;
use PhpParser\Node\Stmt\Foreach_;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = date('Y-m-d', strtotime('-4 months'));
        $endDate = date('Y-m-d');
        $sx_orders = SXOrder::where('cono', 10)->whereBetween('enterdt', [$startDate, $endDate])->get();

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
                    'qty_ship' => $sx_order['qtyship'],
                    'qty_ord' => $sx_order['qty_ord'],
                    'promise_date' => $sx_order['promisedt'],
                    'line_items' => ['line_items' => $line_items ?: []],
                    'is_sales_order' => $this->isSales($line_items),
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
        ->get()->toArray();
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
}
