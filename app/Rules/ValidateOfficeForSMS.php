<?php

namespace App\Rules;

use App\Services\Kenect;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateOfficeForSMS implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $is_valid = false;
        $kenet = new Kenect();
        $locations = array_column(json_decode($kenet->locations()), 'name');

        foreach($locations as $location)
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
