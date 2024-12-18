<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleForm;
use App\Models\Order\Order;
use App\Models\Scheduler\Schedule;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public ScheduleForm $form;

    public $showModal;
    public $name;
    public $schedules;
    public $isEdit;
    public $scheduleOptions;
    protected $listeners = [
        'closeModal' => 'closeModal'
    ];

    public function mount()
    {
        $this->scheduleOptions = collect(ScheduleEnum::cases())
            ->mapWithKeys(fn($case) => [$case->name => $case->value])
            ->toArray();
        $this->getEvents();
    }

    public function create($type)
    {
        $this->isEdit = false;
        $this->form->type = $type;
        $this->showModal = true;

    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->form->reset();
        $this->resetValidation();
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.schedule.index');
    }

    public function submit()
    {
        if( $this->isEdit ) {
            $this->form->update();
            $this->alert('success', 'Schedule Updated');

        } else {
            $this->form->store();
            $this->alert('success', 'New Shipping Scheduled!');
        }

        return redirect()->route('schedule.index');
    }

    public function updatedFormSuffix($value)
    {
        $this->form->getOrderInfo($value);
    }

    public function updatedFormSxOrdernumber($value)
    {
        $this->form->suffix = null;

    }

    public function getEvents()
    {
        $this->schedules = Schedule::all()->map(function($schedule) {
            return [
                'id' => $schedule->id,
                'title' => 'Order #' . $schedule->sx_ordernumber,
                'start' => $schedule->schedule_date,
                'description' => $schedule->type,
            ];
        });
    }

    public function handleEventClick(Schedule $schedule)
    {
        $this->form->init($schedule);
        $this->showModal = true;
        $this->isEdit =true;
        $this->updatedFormSuffix($schedule->order_number_suffix);
    }

}
