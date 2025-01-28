<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateScheduleDate implements ValidationRule
{
    public $holidays;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __construct($data)
    {
        $this->holidays = $data['holidays'];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::parse($value);
        $dateValues = array_column($this->holidays, 'date');
        if (in_array($date->format('Y-m-d'), $dateValues)) {
            $fail('selected date is a holiday');
            return;
        }
    }
}
