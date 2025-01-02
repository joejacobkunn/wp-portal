<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateScheduleTime implements ValidationRule
{
    public $days;
    public $type;
    public $scheduleDate;
    private $pickupDeliveryTimeRange = [
        'morning' => ['start' => '08:00', 'end' => '12:00'],
        'noon' => ['start' => '12:00', 'end' => '16:00'],
        'afternoon' => ['start' => '16:00', 'end' => '19:00'],
        'all_day' => ['start' => '08:00', 'end' => '19:00']
    ];
    private $ahmTimeRange = [
        'am' => ['start' => '09:00', 'end' => '13:00'],
        'pm' => ['start' => '13:00', 'end' => '18:00'],
        'all_day' => ['start' => '09:00', 'end' => '18:00']
    ];
    public function __construct($days, $type, $date)
    {
        $this->days = $days;
        $this->type = $type;
        $this->scheduleDate = $date;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {


        $scheduleTime = Carbon::parse($value);
        $day = Carbon::parse($this->scheduleDate);
        $dayName = strtolower($day->format('l'));

        if(!key_exists($dayName, $this->days)) {
            $fail("Select Valid schedule date first!");
            return;
        }

        $day = $this->days[$dayName];
        if($this->type == 'at_home_maintenance') {
            $period = $this->ahmTimeRange[$day['ahm_shift']];
        }

            if($this->type == 'pickup' || $this->type == 'delivery') {
                $period = $this->pickupDeliveryTimeRange[$day['delivery_pickup_shift']];
            }

            $startTime = Carbon::parse($period['start']);
            $endTime = Carbon::parse($period['end']);
            if (!$scheduleTime->between($startTime, $endTime)) {
                $fail("Selected Time is invalid");
            }
    }

}
