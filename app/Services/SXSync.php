<?php

namespace App\Services;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use App\Models\Order\Order as PortalOrder;
use App\Models\SX\Customer as SXCustomer;
use App\Models\SX\Order;
use Carbon\Carbon;
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

            Log::channel('webhook')->info('customer.create : SX #'.$sx_customer->custno.' ('.trim($sx_customer->name).') created');

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

        Log::channel('webhook')->info('customer.update : SX #'.$sx_customer->custno.' ('.trim($sx_customer->name).') updated');

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

        Log::channel('webhook')->info('customer.sx_number_changed : SX #'.$data['old_sx_customer_number'].' has been assigned a new SX#'.$data['new_sx_customer_number']);

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

        Log::channel('webhook')->info('customer.order_status_changed : SX #'.$data['sx_customer_number'].' has open order count updated to '.$no_open_orders.' from '.$customer->open_order_count);

        return response()->json(['status' => 'success', 'customer_id' => $customer->id, 'open_order_count' => $no_open_orders], 200);
    }

    private function orderShipped($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();

        //if herohub notification if configured
        if ($account->herohubConfig()->exists()) {

            Log::channel('webhook')->info('order.shipped => Sending herohub notification for '.$data['order_no'].'-'.$data['order_suffix']);

            $herohub = new HeroHub($account);

            return response($herohub->send_shipped_notification($data), 200)
                ->header('Content-Type', 'application/json');
        }
    }

    private function orderCreated($data)
    {
        $sx_order = Order::where('cono', $data['cono'])->where('orderno', $data['order_no'])->where('ordersuf',$data['order_suffix'])->first();
        
        $portal_order = PortalOrder::updateOrCreate(
            [
                'order_number' => $data['order_no'], 
                'order_number_suffix' => $data['order_suffix'], 
                'cono' => $data['cono']
            ],
            [
                'whse' => $sx_order['whse'],
                'taken_by' => $sx_order['takenby'],
                'order_date' => Carbon::parse($sx_order['enterdt'])->format('Y-m-d'),
                'stage_code' => $sx_order['stagecd'],
                'sx_customer_number' => $sx_order['custno'],
                'is_sro' => $sx_order['user1'] == 'SRO' ? 1 : 0,
                'ship_via' => $sx_order['shipviaty'],
                'qty_ship' => $sx_order['qtyship'],
                'qty_ord' => $sx_order['qty_ord'],
                'promise_date' => $sx_order['promisedt'],
                'status' => 'Pending Review'
            ]
        );

        return response()->json(['status' => 'success', 'portal_order_id' => $portal_order->id], 201);

    }


    private function split_address($address)
    {
        return explode(';', $address);
    }
}
