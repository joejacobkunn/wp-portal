<?php

namespace App\Http\Livewire\Core\Customer\Traits;

use App\Classes\SX;
use App\Models\Core\Account;
use App\Models\Core\Customer;
use App\Models\Core\Role;
use App\Models\Core\User;
use App\Models\ZipCode;

trait FormRequest
{

    protected $validationAttributes = [
        'customer.name' => 'Customer Name',
        'customer.customer_type' => 'Customer Type',
    ];

    protected $messages = [
        'customer.email.unique' => 'This email address is already associated with an account',
        'customer.phone.unique' => 'This phone number is already associated with an account',
    ];


    protected function rules()
    {
        return [
            'customer.account_id' => 'required',
            'customer.name' => 'required|min:3',
            'customer.customer_type' => 'required',
            'customer.sx_customer_number' => 'nullable',
            'customer.email' => 'sometimes|email|unique:customers,email',
            'customer.phone' => 'sometimes|numeric|digits:10|unique:customers,phone',
            'customer.address' => 'required',
            'customer.address2' => 'nullable',
            'customer.city' => 'required',
            'customer.state' => 'required',
            'customer.zip' => 'sometimes|numeric|exists:zip_codes,zipcode',
            'customer.customer_since' => 'nullable',
            'customer.look_up_name' => 'nullable',
            'customer.sales_territory' => 'nullable',
            'customer.last_sale_date' => 'nullable',
            'customer.sales_rep_in' => 'nullable',
            'customer.sales_rep_out' => 'nullable',
            'customer.is_active' => 'required',
            'customer.open_order_count' => 'required|numeric'
        ];
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        if (empty($this->customer->id)) {
            $this->customer = new Customer();
            $this->customer->account_id = $this->account->id;
            $this->customer->sx_customer_number = null;
            $this->customer->name = null;
            $this->customer->customer_type = null;
            $this->customer->phone = null;
            $this->customer->email = null;
            $this->customer->address = null;
            $this->customer->address2 = null;
            $this->customer->city = null;
            $this->customer->state = null;
            $this->customer->zip = null;
            $this->customer->customer_since = now()->format('Y-m-d');
            $this->customer->look_up_name = null;
            $this->customer->sales_territory = null;
            $this->customer->last_sale_date = null;
            $this->customer->sales_rep_in = null;
            $this->customer->sales_rep_out = null;
            $this->customer->is_active = 1;
            $this->customer->open_order_count = 0;
        } else {
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->authorize('store', Customer::class);
        
        $this->validate();

        if (! empty($this->customer->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new account
     */
    public function store()
    {
        $sx_customer_number = $this->createSXCustomer();
        
        if($sx_customer_number){
            $this->customer->sx_customer_number = $sx_customer_number;
            $this->customer->sales_rep_in = auth()->user()->sx_operator_id;
            $this->customer->sales_rep_out = auth()->user()->sx_operator_id;
            $this->customer->save();
            $this->alert('success', 'Customer created!');

            if (empty($this->sourcePopup)) {
                return redirect()->route('core.customer.show', $this->customer);
            } else {
                $this->dispatch('customer:created', $this->customer->id);
                return;
            }

        }else{
            return $this->alert('warning', 'Something went wrong. Try again later');
        }
    }

    /**
     * Update existing account
     */
    public function update()
    {
    }


    public function updatedCustomerZip()
    {
        //find state and city using zip
        $zip_data = ZipCode::where('zipcode', $this->customer->zip)->first();
        $this->customer->city = $zip_data->city;
        $this->customer->state = $zip_data->state;
    }

    public function createSXCustomer()
    {
        $data = [
            'request' => [
              'companyNumber' => 10,
              'operatorInit' => 'web',
              'operatorPassword' => '',
              'tMntTt' => [
                't-mnt-tt' => [
                   [
                    'setNo' => 1,
                    'seqNo' => 1,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'name',
                    'fieldValue' => $this->customer->name,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 2,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'lookupnm',
                    'fieldValue' => $this->customer->name,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 3,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'addr1',
                    'fieldValue' => $this->customer->address,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 4,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'addr2',
                    'fieldValue' => $this->customer->address2,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 5,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'addr3',
                    'fieldValue' => '',
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 6,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'countrycd',
                    'fieldValue' => 'US',
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 7,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'city',
                    'fieldValue' => $this->customer->city,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 8,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'state',
                    'fieldValue' => $this->customer->state,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 9,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'zipcd',
                    'fieldValue' => $this->customer->zip,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 10,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'custtype',
                    'fieldValue' => $this->customer->customer_type,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 11,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'phoneno',
                    'fieldValue' => $this->customer->phone,
                  ],
                   [
                    'setNo' => 1,
                    'seqNo' => 12,
                    'key1' => '',
                    'key2' => '',
                    'updateMode' => 'add',
                    'fieldName' => 'email',
                    'fieldValue' => $this->customer->email,
                  ],
                ],
              ],
              'extraData' => '',
            ],
        ];

        $sx = new SX();
        $response = $sx->create_customer($data);

        if($response['status'] == 'success'){
            return $response['sx_customer_number'];
        }else{
            return null;
        }
    }

}
