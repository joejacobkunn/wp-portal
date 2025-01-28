<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTrucksforSchedule implements ValidationRule
{
    public $truckName;

    public function __construct($data)
    {
        $this->truckName = $data;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->truckName != $value) {
            $fail("Truck name not matching");
        }
    }
}
