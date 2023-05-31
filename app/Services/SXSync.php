<?php

namespace App\Services;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use App\Models\SX\Customer as SXCustomer;

class SXSync {

    protected $payload;

    public function __construct()
    {
    }

    public function sync($webhook)
    {
        $this->payload = $webhook->payload;

        if($this->payload['event'] == 'customer.create') $this->createCustomer($this->payload['data']);
        
    }

    private function createCustomer($data)
    {
        $sx_customer = SXCustomer::where('cono', $data['cono'])->where('custno', $data['sx_customer_number'])->first();
        
        //create customer
        $account = Account::where('sx_company_number', $data['cono'])->first();

        $address = $this->split_address($sx_customer->addr);
        
        $customer = Customer::create([
            'account_id' => $account->id,
            'sx_customer_number' => $sx_customer->custno,
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

        ]);
    }

    private function split_address($address)
    {
        return explode(';', $address);
    }

}