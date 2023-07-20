<?php

namespace App\Rules;

use App\Models\Core\Account;
use App\Models\SX\Customer;
use App\Models\SX\DealerInfo;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCustomer implements ValidationRule
{
    private $company_number;

    private $customer_type;

    public function __construct($customer_type)
    {
        $this->customer_type = $customer_type;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $customer_number, Closure $fail): void
    {
        $account = Account::find(app('domain')->getClientId());

        if ($this->customer_type == 'exmark') {
            //if exmark , lets query the zzarscc table and get the actual sx customer number
            $dealer = DealerInfo::select('custno')
                ->where('cono', $account->sx_company_number)
                ->where('exmarknm', $customer_number)->first();

            if (is_null($dealer)) {
                $fail('The :attribute is not found in our system');
            }

        } else {

            $customer = Customer::where('cono', $account->sx_company_number)
                ->where('custno', $customer_number)->first();

            if (is_null($customer)) {
                $fail('The :attribute is not found in our system');
            }

        }

    }
}
