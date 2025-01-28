<?php
 namespace App\Http\Livewire\Scheduler\Truck\Form;

use App\Models\Core\User;
use App\Models\Scheduler\Truck;
use App\Models\Scheduler\TruckSchedule;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ScheduleForm extends Form
{
    public ?TruckSchedule $truckSchedule;

    public $zone;
    public $start_time;
    public $end_time;
    public $schedule_date;
    public $timePeriod = 'AM';
    public $timePeriodEnd = 'AM';
    public $slots;

    protected $validationAttributes = [
        'zone' => 'Zone',
        'start_time' => 'Start Time',
        'timePeriod' => 'Time Period',
        'timePeriodEnd' => 'Time Period',
        'end_time' => 'Schedule Date',
        'slots' => 'Slots',
    ];

    protected function rules()
    {
        return [
            'zone' => 'required|exists:zones,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'timePeriod' => 'required',
            'timePeriodEnd' => 'required',
            'slots' => 'required'
        ];

    }

    public function store(Truck $truck)
    {
        $this->validate();
        return TruckSchedule::create(
            [
                'truck_id' => $truck->id,
                'zone_id' => $this->zone,
                'schedule_date' => $this->schedule_date,
                'start_time' => $this->start_time.' '.$this->timePeriod,
                'end_time' => $this->end_time. ' '.$this->timePeriodEnd,
                'slots' => $this->slots
            ]
        );
    }

    public function setScheduleDate($date)
    {
        $this->schedule_date = $date;
    }

    public function init(TruckSchedule $schedule)
    {
        $this->truckSchedule = $schedule;
        $this->fill($schedule->toArray());
        $this->zone = $schedule->zone_id;
        $start = explode(" ", $schedule->start_time);
        $end = explode(" ", $schedule->end_time);
        $this->start_time = $start[0];
        $this->timePeriod = $start[1];
        $this->end_time = $end[0];
        $this->timePeriodEnd = $end[1];
    }

    public function update(Truck $truck)
    {
        $this->truckSchedule->fill([
            'zone_id' => $this->zone,
            'schedule_date' => $this->schedule_date,
            'start_time' => $this->start_time.' '.$this->timePeriod,
            'end_time' => $this->end_time. ' '.$this->timePeriodEnd,
            'slots' => $this->slots
        ]);
        $this->truckSchedule->save();
        return $this->truckSchedule;
    }
}
