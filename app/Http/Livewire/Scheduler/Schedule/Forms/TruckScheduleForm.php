<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Models\Scheduler\TruckSchedule;
use Livewire\Form;

 class TruckScheduleForm extends Form
 {
    public ?TruckSchedule $schedule;

    public $slots;

    protected $validationAttributes = [
        'slots' => 'Slots',
    ];

    protected $rules = [
        'slots' =>'required|integer'
    ];

    public function init(TruckSchedule $schedule)
    {
        $this->schedule = $schedule;
        $this->fill($schedule->toArray());
    }

    public function update()
    {
        $this->schedule->update([
            'slots' => $this->slots
        ]);
    }
 }
