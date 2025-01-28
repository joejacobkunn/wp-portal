<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateScheduleTime implements ValidationRule
{
    public $holidays;
    public $shift;
    public $timeStatus = false;
    public $scheduleDate;
    private $pickupDeliveryTimeRange = [
        'morning' => ['start' => '08:00', 'end' => '12:00' , 'label' => '8AM - 12PM'],
        'noon' => ['start' => '12:00', 'end' => '16:00' , 'label' => '12PM - 4PM'],
        'afternoon' => ['start' => '16:00', 'end' => '19:00', 'label' => '4PM - 7PM'],
        'all_day' => ['start' => '08:00', 'end' => '16:00', 'label' => '9AM - 4PM']
    ];
    private $ahmTimeRange = [
        'am' => ['start' => '09:00', 'end' => '13:00', 'label' => '9AM - 1PM'],
        'pm' => ['start' => '13:00', 'end' => '18:00', 'label' => '1PM - 6PM'],
        'all_day' => ['start' => '09:00', 'end' => '16:00' , 'label' => '9AM - 4PM']
    ];
    public function __construct($data, $date)
    {
        $this->holidays = $data['holidays'];
        $this->shift = $data['shift'];
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
        $selectedDay = strtolower($day->format('l'));
        $month = strtolower($day->format('F'));
        if (!in_array($month, array_map('strtolower', array_keys($this->shift->shift)))) {
            $fail('Select valid date first');
            return;
        }
        $days = array_keys($this->shift->shift[$month]);
        if (!in_array($selectedDay, array_map('strtolower', $days))) {
            $fail('Select valid date first');
            return;
        }

        if($this->shift->type == 'ahm') {
            foreach($this->shift->shift[$month][$selectedDay] as $data) {
                $matchedKey = array_filter($this->ahmTimeRange, function($slot) use( $data) {
                    return $slot['label'] === $data['shift'];
                });
                $matchedTimeRange = reset($matchedKey);
                $startTime = Carbon::parse($matchedTimeRange['start']);
                $endTime = Carbon::parse($matchedTimeRange['end']);
                if ($scheduleTime->between($startTime, $endTime)) {
                    $this->timeStatus = true;
                }
            }
        }

        if($this->shift->type == 'pickup_delivery') {
            foreach($this->shift->shift[$month][$selectedDay] as $data) {
                $matchedKey = array_filter($this->pickupDeliveryTimeRange, function($slot) use( $data) {
                    return $slot['label'] === $data['shift'];
                });
                $matchedTimeRange = reset($matchedKey); // Gets just the first (matched) subarray
                $startTime = Carbon::parse($matchedTimeRange['start']);
                $endTime = Carbon::parse($matchedTimeRange['end']);
                if ($scheduleTime->between($startTime, $endTime)) {
                    $this->timeStatus = true;
                }
            }

        }
        if(!$this->timeStatus) {
            $fail("Selected Time is invalid");
            return;
        }

    }

}
