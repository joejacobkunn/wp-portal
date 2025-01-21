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
        $pattern = '/^(0[0-9]|1[0-2]):[0-5][0-9]\s+(AM|PM|am|pm)\s+-\s+(0[0-9]|1[0-2]):[0-5][0-9]\s+(AM|PM|am|pm)$/';

        if (!preg_match($pattern, $value)) {
            $fail("The {$attribute} must be in the format '09:00 AM - 10:00 PM'.");
        }

        if (preg_match($pattern, $value)) {
            [$startTime, $endTime] = explode(' - ', $value);
            $startTimestamp = strtotime($startTime);
            $endTimestamp = strtotime($endTime);

            if ($startTimestamp >= $endTimestamp) {
                $fail("The end time must be after the start time in {$attribute}.");
            }
        }
    }
}
