<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleForm;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;

    public ScheduleForm $form;

    public $showModal;
    public $name;
    public $schedules;
    public $isEdit;
    public $showView;
    public $addressModal;
    public $orderInfoStrng;
    public $scheduleOptions;
    public $warehouses;
    public $holidays;
    public $activeWarehouse;
    protected $listeners = [
        'closeModal' => 'closeModal',
        'edit' => 'edit',
        'deleteRecord' => 'delete',
        'typeCheck' => 'typeCheck',
        'closeAddress' => 'closeAddress'
    ];

    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => 'Confirm Delete',
            'listener' => 'deleteRecord',
        ],
    ];

    public function mount()
    {
        $this->authorize('viewAny', Schedule::class);
        $this->orderInfoStrng = uniqid();
        $this->scheduleOptions = collect(ScheduleEnum::cases())
            ->mapWithKeys(fn($case) => [$case->name => $case->icon().' '.$case->value])
            ->toArray();

        $this->warehouses = Warehouse::select(['id', 'short', 'title'])->get();
        if(Auth::user()->office_location) {
            $this->activeWarehouse = $this->warehouses->where('title', Auth::user()->office_location)->first();
        }
        $holidays = CalendarHoliday::listAll();
        $this->getEvents();

        $this->holidays = collect($holidays)->map(function ($holiday) {
            return [
                'title' => $holiday['label'],
                'start' => $holiday['date'],
                'color' => '#ff9f89',
                'className' => 'holiday-event',
                'description' => 'holiday',
            ];
        })->toArray();
    }

    public function create($type)
    {
        $this->authorize('store', Schedule::class);
        $this->isEdit = false;
        $this->form->reset();
        $this->form->type = $type;
        $this->showModal = true;

    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showView = false;
        $this->isEdit = false;
        $this->resetValidation();
        $this->form->reset();
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.schedule.index');
    }

    public function submit()
    {
        if( $this->isEdit ) {

            $this->authorize('update', $this->form->schedule);
            $this->form->update();
            $this->alert('success', 'Schedule Updated');

        } else {
            $this->authorize('store', Schedule::class);
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

    public function typeCheck($field, $value)
    {
        $this->form->type = $value;
        $this->form->checkServiceValidity($value);
    }

    public function getEvents()
    {
        $whse = $this->activeWarehouse?->short;

        $this->schedules = Schedule::with('order')
        ->whereHas('order', function ($query) use ($whse) {
            $query->where('whse', strtolower($whse));
        })

        ->get()
        ->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'title' => 'Order #' . $schedule->sx_ordernumber,
                'start' => $schedule->schedule_date,
                'description' => 'schedule',
            ];
        });

    }

    public function edit()
    {
        $this->showView = false;
    }

    public function delete()
    {
        $this->authorize('delete', $this->form->schedule);
        $this->form->delete();
        $this->alert('success', 'Record Deleted!');
        return redirect()->route('schedule.index');

    }

    public function handleEventClick(Schedule $schedule)
    {
        $this->form->init($schedule);
        $this->orderInfoStrng = uniqid();
    }

    public function showAdrress()
    {
        $this->form->getRecomAddress();
        $this->addressModal = true;
    }

    public function closeAddress()
    {
        $this->addressModal = false;
    }

    public function setAddress()
    {
        $this->form->setAddress();
        $this->closeAddress();
    }

    public function changeWarehouse($wsheID)
    {
        $this->activeWarehouse = $this->warehouses->find($wsheID);
        $this->getEvents();
        $this->dispatch('calendar-needs-update',  $this->activeWarehouse->title);

    }

}
