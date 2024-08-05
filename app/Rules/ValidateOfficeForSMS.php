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
        $kenet = new Kenect();
        $locations = array_column(json_decode($kenet->locations()), 'name');
        if (!in_array($value, $locations)) {
            $fail("Location not found");
        }
    }
}
