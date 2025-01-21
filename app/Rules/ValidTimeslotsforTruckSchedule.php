<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidTimeslotsforTruckSchedule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Pattern for format like "9am-1pm" (accepts 1-12 followed by am/pm)
        $pattern = '/^([1-9]|1[0-2])(am|pm)\s*-\s*([1-9]|1[0-2])(am|pm)$/i';

        if (!preg_match($pattern, $value, $matches)) {
            $fail("The {$attribute} must be in the format '9am-1pm'.");
            return;
        }

        // Split the time range and convert to timestamps
        [$startTime, $endTime] = explode('-', str_replace(' ', '', $value));
        $startTimestamp = strtotime($startTime);
        $endTimestamp = strtotime($endTime);

        if ($startTimestamp >= $endTimestamp) {
            $fail("The end time must be after the start time in {$attribute}.");
        }
    }
}
