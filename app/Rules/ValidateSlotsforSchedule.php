<?php

namespace App\Rules;

use App\Models\Scheduler\TruckSchedule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateSlotsforSchedule implements ValidationRule
{
    public $type;

    public function __construct($type)
    {
        $this->type = $type;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->type == 'schedule_override') {
            return;
        }

        $truckSchedule = TruckSchedule::find($value);
        if ($truckSchedule->slots <= $truckSchedule->scheduleCount) {
            $fail("Selected timeslot if full");
        }
    }
}
