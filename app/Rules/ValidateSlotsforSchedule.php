<?php

namespace App\Rules;

use App\Models\Scheduler\TruckSchedule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateSlotsforSchedule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $truckSchedule = TruckSchedule::find($value);
        if ($truckSchedule->slots <= $truckSchedule->scheduleCount) {
            $fail("Selected timeslot if full");
        }
    }
}
