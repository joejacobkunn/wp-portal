<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateOfficeForSMS implements ValidationRule
{
    public $locations;

    public function __construct($locations = [])
    {
        $this->locations = $locations;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $is_valid = false;

        foreach($this->locations as $location)
        {
            if (str_contains(strtolower(trim($location)),strtolower(trim($value)))) {
                $is_valid = true;
            }
        }

        if (!$is_valid) {
            $fail("Location not found");
        }
    }
}
