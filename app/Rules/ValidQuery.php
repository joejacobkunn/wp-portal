<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Exception;

class ValidQuery implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try
        {
            DB::connection('sx')->select($value);
        }
        catch(Exception $e)
        {
            $fail('The :attribute has errors.');
        }
        
        
    }
}
