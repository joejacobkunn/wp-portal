<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidProductForWarrantyRegistration implements ValidationRule
{
    public $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(config('sx.mock')) {
            $value = mt_rand(0,1);
            if(!$value) $fail('The product/serial could not be found');
        } else{
            $icses = DB::connection('sx')->select("select * from pub.icses where cono = ? and LOWER(prod) = ? and LOWER(serialno) = ? and whseto = ? and currstatus = ? with (nolock)", [$this->data['cono'], strtolower($this->data['prod']), strtolower($this->data['serialno']), '', 's']);

            if(count($icses) != 1) $fail('The product/serial could not be found');

        }
    }
}
