<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateScheduleDate implements ValidationRule
{
    public $days;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __construct($days)
    {
        $this->days = $days;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::parse($value);
        $dayName = strtolower($date->format('l'));
        $days = array_keys($this->days);

        if (!in_array($dayName, array_map('strtolower', $days))) {
            $fail('selected Date is not valid');
        }
    }
}
