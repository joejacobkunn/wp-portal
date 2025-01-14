<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateScheduleDate implements ValidationRule
{
    public $holidays;
    public $shift;
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __construct($data)
    {
        $this->holidays = $data['holidays'];
        $this->shift = $data['shift'];
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::parse($value);
        $dateValues = array_column($this->holidays, 'date');
        if (in_array($date->format('Y-m-d'), $dateValues)) {
            $fail('selected date is a holiday');
            return;
        }
        $selectedDay = strtolower($date->format('l'));
        $month = strtolower($date->format('F'));
        if (!in_array($month, array_map('strtolower', array_keys($this->shift->shift)))) {
            $fail('service not available in this month');
            return;
        }
        $days = array_keys($this->shift->shift[$month]);
        if (!in_array($selectedDay, array_map('strtolower', $days))) {
            $fail('selected date is not valid');
        }
    }
}
