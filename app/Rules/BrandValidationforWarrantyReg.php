<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BrandValidationforWarrantyReg implements ValidationRule
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
        if (!in_array(strtolower($value), $this->data)) {
            $fail("Brand Not found in Configurator");
        }
    }
}
