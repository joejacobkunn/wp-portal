<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Models\Scheduler\Schedule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Form;

class ScheduleViewForm extends Form
{
    public ?Schedule $schedule;
    public $cancel_reason;

    protected $validationAttributes = [
        'cancel_reason' => 'Reason',
    ];

    protected $rules =  [
        'cancel_reason' => 'required|string|max:220'
    ];

    public function init(Schedule $schedule)
    {
        $this->schedule = $schedule;
    }
    public function update()
    {
        $validData = $this->validateOnly('cancel_reason');
        $this->schedule->fill($validData);
        $this->schedule->status = 'Cancelled';
        $this->schedule->save();
    }
}
