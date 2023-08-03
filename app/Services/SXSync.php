<?php

namespace App\Services;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use App\Models\SX\Customer as SXCustomer;
use App\Models\SX\Order;

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

    }

    private function createCustomer($data)
    {
        $sx_customer = SXCustomer::where('cono', $data['cono'])->where('custno', $data['sx_customer_number'])->first();

        $account = Account::where('sx_company_number', $data['cono'])->first();

        $address = $this->split_address($sx_customer->addr);

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

        $address = $this->split_address($sx_customer->addr);

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

        return response()->json(['status' => 'success', 'customer_id' => $customer->id], 200);

    }

    private function updateCustomerSXNumber($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();
        
        if(!empty($data['old_sx_customer_number']) && !empty($data['new_sx_customer_number'])){
            $customer = Customer::where('account_id', $account->id)->where('sx_customer_number', $data['old_sx_customer_number'])->first();
            $customer->update(['sx_customer_number' => $data['new_sx_customer_number']]);
        }

        return response()->json(['status' => 'success', 'customer_id' => $customer->id], 200);
    }

    private function updateCustomerOpenOrderStatus($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();

        $customer = Customer::where('account_id', $account->id)->where('sx_customer_number', $data['sx_customer_number'])->first();
        $no_open_orders = Order::where('cono', $data['cono'])->where('custno', $data['sx_customer_number'])->openOrders()->count();

        if ($no_open_orders > 0) {
            $customer->update(['has_open_order' => 1]);
        } else {
            $customer->update(['has_open_order' => 0]);
        }

        return response()->json(['status' => 'success', 'customer_id' => $customer->id, 'has_open_order' => ($no_open_orders == 0) ? 0 : 1], 200);
    }

    private function orderShipped($data)
    {
        $account = Account::where('sx_company_number', $data['cono'])->first();

        //if herohub notification if configured
        if ($account->herohubConfig()->exists()) {

            $herohub = new HeroHub($account);

            return response($herohub->send_shipped_notification($data), 200)
                ->header('Content-Type', 'application/json');
        }
    }

    private function split_address($address)
    {
        return explode(';', $address);
    }
}
