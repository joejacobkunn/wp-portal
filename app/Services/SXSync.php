<?php

namespace App\Services;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use App\Models\Order\Order as PortalOrder;
use App\Models\Scheduler\Schedule;
use App\Models\SX\Customer as SXCustomer;
use App\Models\SX\Order;
use App\Models\SX\OrderLineItem;
use Carbon\Carbon;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;

class SXSync
{
    protected $payload;

    public function __construct()
    {
    }

    public function sync($webhook)
    {
        $this->payload = $webhook->payload;

        if ($this->payload['event'] == 'customer.create') {
            $this->createCustomer($this->payload['data']);
        }

        if ($this->payload['event'] == 'customer.update') {
            $this->updateCustomer($this->payload['data']);
        }

        if ($this->payload['event'] == 'customer.order_status_changed') {
            $this->updateCustomerOpenOrderStatus($this->payload['data']);
        }

        if ($this->payload['event'] == 'customer.sx_number_changed') {
            $this->updateCustomerSXNumber($this->payload['data']);
        }

        if ($this->payload['event'] == 'order.shipped') {
            $this->orderShipped($this->payload['data']);
        }

        if ($this->payload['event'] == 'order.created') {
            $this->orderCreated($this->payload['data']);
        }

        if ($this->payload['event'] == 'serialized-products.invoiced') {
            $this->serializedProductsInvoiced($this->payload['data']);
        }



    }

    private function createCustomer($data)
    {
        $sx_customer_number = $data['sx_customer_number'] ?? $data['old_sx_customer_number'];

        $sx_customer = SXCustomer::where('cono', $data['cono'])->where('custno', $sx_customer_number)->first();

        if(is_null($sx_customer)) return 0;

        $account = Account::where('sx_company_number', $data['cono'])->first();

        $address = $this->split_address($sx_customer->addr ?? '');

        //create customer in mysql table

        $customer = Customer::firstOrCreate(
            [
                'account_id' => $account->id,
                'sx_customer_number' => $sx_customer->custno,

            ],

            [
                'account_id' => $account->id,
                'sx_customer_number' => $sx_customer->custno,
                'name' => trim($sx_customer->name),
                'customer_type' => $sx_customer->custtype,
                'phone' => $sx_customer->phoneno,
                'email' => $sx_customer->email,
                'address' => $address[0],
                'address2' => $address[1] ?? '',
                'city' => $sx_customer->city,
                'state' => $sx_customer->state,
                'zip' => $sx_customer->zipcd,
                'customer_since' => date('Y-m-d', strtotime($sx_customer->enterdt)),
                'look_up_name' => $sx_customer->lookupnm,
                'sales_territory' => $sx_customer->salesterr,
                'last_sale_date' => $sx_customer->lastsaledt,
                'sales_rep_in' => $sx_customer->slsrepin,
                'sales_rep_out' => $sx_customer->slsrepout,
                'is_active' => $sx_customer->statustype ?? 1,

            ]);


        return response()->json(['status' => 'success', 'customer_id' => $customer->id], 201);
    }

    private function updateCustomer($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();

        $sx_customer = SXCustomer::where('cono', $data['cono'])->where('custno', $data['sx_customer_number'])->first();

        if(is_null($sx_customer)) return 0;

        $address = $this->split_address($sx_customer->addr ?? '');

        $customer = Customer::updateOrCreate(
            [
                'account_id' => $account->id,
                'sx_customer_number' => $sx_customer->custno,

            ],
            [
                'name' => $sx_customer->name,
                'customer_type' => $sx_customer->custtype,
                'phone' => $sx_customer->phoneno,
                'email' => $sx_customer->email,
                'address' => $address[0],
                'address2' => $address[1] ?? '',
                'city' => $sx_customer->city,
                'state' => $sx_customer->state,
                'zip' => $sx_customer->zipcd,
                'customer_since' => date('Y-m-d', strtotime($sx_customer->enterdt)),
                'look_up_name' => $sx_customer->lookupnm,
                'sales_territory' => $sx_customer->salesterr,
                'last_sale_date' => $sx_customer->lastsaledt,
                'sales_rep_in' => $sx_customer->slsrepin,
                'sales_rep_out' => $sx_customer->slsrepout,
                'is_active' => $sx_customer->statustype ?? 1,
            ]
        );

        //Log::channel('webhook')->info('customer.update : SX #'.$sx_customer->custno.' ('.trim($sx_customer->name).') updated');

        return response()->json(['status' => 'success', 'customer_id' => $customer->id], 200);

    }

    private function updateCustomerSXNumber($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();
        
        if(!empty($data['old_sx_customer_number']) && !empty($data['new_sx_customer_number'])){
            $customer = Customer::where('account_id', $account->id)->where('sx_customer_number', $data['old_sx_customer_number'])->first();
            
            if(is_null($customer) || empty($customer)) 
            {
                $this->createCustomer($data);
                $customer = Customer::where('account_id', $account->id)->where('sx_customer_number', $data['old_sx_customer_number'])->first();
            }

            if(is_null($customer)){
                return Log::channel('webhook')->warning('customer.sx_number_changed : SX #'.$data['old_sx_customer_number'].' couldnt find or create customer ', $data);
            }


            $sx_customer = SXCustomer::where('cono', $data['cono'])->where('custno', $data['new_sx_customer_number'])->first();
            $address = $this->split_address($sx_customer->addr ?? '');


            $customer->update([
                'name' => $sx_customer->name,
                'sx_customer_number' => $data['new_sx_customer_number'],
                'customer_type' => $sx_customer->custtype,
                'phone' => $sx_customer->phoneno,
                'email' => $sx_customer->email,
                'address' => $address[0],
                'address2' => $address[1] ?? '',
                'city' => $sx_customer->city,
                'state' => $sx_customer->state,
                'zip' => $sx_customer->zipcd,
                'customer_since' => date('Y-m-d', strtotime($sx_customer->enterdt)),
                'look_up_name' => $sx_customer->lookupnm,
                'sales_territory' => $sx_customer->salesterr,
                'last_sale_date' => $sx_customer->lastsaledt,
                'sales_rep_in' => $sx_customer->slsrepin,
                'sales_rep_out' => $sx_customer->slsrepout,
                'is_active' => $sx_customer->statustype ?? 1,
                ]
            );
        }

        //Log::channel('webhook')->info('customer.sx_number_changed : SX #'.$data['old_sx_customer_number'].' has been assigned a new SX#'.$data['new_sx_customer_number']);

        return response()->json(['status' => 'success', 'customer_id' => $customer->id], 200);
    }

    private function updateCustomerOpenOrderStatus($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();

        $customer = Customer::where('account_id', $account->id)->where('sx_customer_number', $data['sx_customer_number'])->first();
        
        if(is_null($customer) || empty($customer)) 
        {
            $this->createCustomer($data);
            $customer = Customer::where('account_id', $account->id)->where('sx_customer_number', $data['sx_customer_number'])->first();
        }

        if(is_null($customer)){
            return Log::channel('webhook')->warning('customer.order_status_changed : SX #'.$data['sx_customer_number'].' couldnt find or create customer ', $data);
        }


        $no_open_orders = Order::where('cono', $data['cono'])->where('custno', $data['sx_customer_number'])->openOrders()->count();

        $customer->update(['open_order_count' => $no_open_orders]);

        //Log::channel('webhook')->info('customer.order_status_changed : SX #'.$data['sx_customer_number'].' has open order count updated to '.$no_open_orders.' from '.$customer->open_order_count);

        return response()->json(['status' => 'success', 'customer_id' => $customer->id, 'open_order_count' => $no_open_orders], 200);
    }

    private function orderShipped($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();

        //if herohub notification if configured
        if ($account->herohubConfig()->exists()) {

            //Log::channel('webhook')->info('order.shipped => Sending herohub notification for '.$data['order_no'].'-'.$data['order_suffix']);

            $herohub = new HeroHub($account);

            return response($herohub->send_shipped_notification($data), 200)
                ->header('Content-Type', 'application/json');
        }
    }

    private function orderCreated($data)
    {
        $sx_order = Order::select(['cono', 'orderno','ordersuf','takenby', 'enterdt', 'stagecd', 'custno', 'user1', 'shipviaty', 'promisedt', 'stagecd', 'totqtyshp', 'totqtyord', 'whse', 'user6', 'shiptoaddr', 'shiptonm', 'shiptost', 'shiptozip', 'shiptocity', 'shipinstr', 'shipto'])->where('cono', $data['cono'])->where('orderno', $data['order_no'])->where('ordersuf',$data['order_suffix'])->first();
        $line_items = $this->getSxOrderLineItemsProperty($data['order_no'],$data['order_suffix']);
        $wt_status = $this->checkForWarehouseTransfer($sx_order,$line_items);
        
        $portal_order = PortalOrder::updateOrCreate(
            [
                'order_number' => $data['order_no'], 
                'order_number_suffix' => $data['order_suffix'], 
                'cono' => $data['cono']
            ],
            [
                'whse' => strtolower($sx_order['whse']),
                'taken_by' => $sx_order['takenby'],
                'order_date' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                'stage_code' => $sx_order['stagecd'],
                'sx_customer_number' => $sx_order['custno'],
                'is_sro' => $this->isSro($sx_order,$line_items->toArray()),
                'ship_via' => strtolower($sx_order['shipviaty']),
                'qty_ship' => $sx_order['totqtyshp'],
                'qty_ord' => $sx_order['totqtyord'],
                'promise_date' => $sx_order['promisedt'],
                'line_items' => ['line_items' => $line_items->toArray() ?: []],
                'is_sales_order' => $this->isSales($line_items->toArray()),
                'is_web_order' => $sx_order['user6'] == '6' ? 1 : 0,
                'warehouse_transfer_available' => ($wt_status == 'wt') ? true : false,
                'partial_warehouse_transfer_available' => ($wt_status == 'p-wt') ? true : false,
                'golf_parts' => $sx_order['user6'] == '6' ? $sx_order->hasGolfParts($line_items) : null,
                'non_stock_line_items' => $sx_order->hasNonStockItems($line_items),
                'last_line_added_at' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                'status' => 'Pending Review',
                'shipping_info' => $sx_order->constructAddress($sx_order)
            ]
        );
        

        return response()->json(['status' => 'success', 'portal_order_id' => $portal_order->id], 201);

    }

    private function serializedProductsInvoiced($data)
    {
        //find if orderno is scheduled
        $schedule = Schedule::where('sx_ordernumber',$data['order_no'])->where('order_number_suffix', $data['order_suffix'])->first();

        if(!is_null($schedule) && !empty($schedule->line_item))
        {
            $prod_code = array_keys($schedule->line_item)[0];

            $line_items = $this->getSxOrderLineItemsProperty($data['order_no'], $data['order_suffix']);

            foreach($line_items as $line)
            {
                if($line->lineno == $data['lineno'])
                {
                    if(strtolower($line->shipprod) == strtolower($prod_code))
                    {
                        $schedule->update(['serial_no' => $data['serialno']]);
                    }
                }
            }
        }
    }



    private function split_address($address)
    {
        return explode(';', $address);
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
            'oeel.enterdt',
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
        ->where('oeel.specnstype', '<>', 'l')
        ->orderBy('oeel.lineno', 'asc')
        ->get();
    }

    private function isSales($line_items)
    {
        foreach($line_items as $line_item)
        {
            if(!str_contains($line_item['prodline'], 'LR-E') && str_contains($line_item['prodline'], '-E'))
            {
                return true;
            } 
        }

        return false;
    }

    private function isSro($order,$line_items)
    {
        $is_sro = $order['user1'] == 'SRO' ? 1 : 0;

        if($is_sro) return true;

        foreach($line_items as $line_item)
        {
            if(str_contains($line_item['prodline'], 'LR-E'))
            {
                return true;
            } 
        }

        return false;

    }

    private function checkForWarehouseTransfer($sx_order, $line_items)
    {
        $line_item_level_statuses = [];

        if($sx_order->isBackOrder())
        {
            foreach($line_items as $line_item)
            {
                $backorder_count = intval($line_item->stkqtyord) - intval($line_item->stkqtyship);

                if($backorder_count > 0 && strtolower($line_item->ordertype) != 't' && strtolower($line_item->specnstype) != 'l')
                {
                    $inventory_levels = $line_item->checkInventoryLevelsInWarehouses(array_diff(['ann','ceda','farm','livo','utic','wate', 'zwhs', 'ecom'], [strtolower($line_item->whse)]));

                    foreach($inventory_levels as $inventory_level)
                    {
                        $available_stock = $inventory_level->qtyonhand - ($inventory_level->qtycommit + $inventory_level->qtyreservd);

                        if($available_stock >= $backorder_count) $line_item_level_statuses[] = 'wt'; //full wt available
                        if(!($available_stock >= $backorder_count) && $available_stock > 0) $line_item_level_statuses[] = 'p-wt'; //partial wt transfer

                    }
                }
            }
        }

        if(in_array('wt',$line_item_level_statuses)) return 'wt';
        if(in_array('p-wt',$line_item_level_statuses)) return 'p-wt';

        return 'n/a';

    }


}
