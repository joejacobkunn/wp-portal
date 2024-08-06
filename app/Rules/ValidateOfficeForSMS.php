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
        $formatted_locations = [];
        $kenet = new Kenect();
        $locations = array_column(json_decode($kenet->locations()), 'name');

        foreach($locations as $location)
        {
            $formatted_locations[] = str_replace('Weingartz - ', '', $location);
        }

        if (!in_array(trim($value), $formatted_locations)) {
            $fail("Location not found");
        }
    }
}
