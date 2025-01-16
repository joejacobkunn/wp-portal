<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleForm;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\ShiftRotation;
use App\Models\Scheduler\Shifts;
use App\Models\Scheduler\Truck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;

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
    public $dateSelected;
    public $holidays;
    public $shifts;
    public $eventStart;
    public $eventEnd;
    public $activeType;
    public $truckInfo = [];
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
        $this->warehouses = Warehouse::select(['id', 'short', 'title'])->where('cono', 10)->orderBy('title', 'asc')->get();

        $query = $this->warehouses;
        if(Auth::user()->office_location) {
           $query = $query->where('title', Auth::user()->office_location);
        }
        $this->activeWarehouse = $query->first();

        $this->activeType = '';
        $holidays = CalendarHoliday::listAll();

        $this->holidays = collect($holidays)->map(function ($holiday) {
            return [
                'title' => $holiday['label'],
                'start' => $holiday['date'],
                'color' => '#ff9f89',
                'className' => 'holiday-event',
                'description' => 'holiday',
            ];
        })->toArray();

        //to get shift data
       $this->handleDateClick(Carbon::now());
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
        $query = Schedule::with('order');
        if($this->activeType && $this->activeType != '') {
            $query->where('type', $this->activeType);
        }
        $query->whereBetween('schedule_date', [$this->eventStart, $this->eventEnd])
        ->whereHas('order', function ($query) use ($whse) {
            $query->where('whse', strtolower($whse));
        });

        $this->schedules =  $query->get()
        ->map(function ($schedule) {
            $type = Str::title(str_replace('_', ' ', $schedule->type));
            $enumInstance = ScheduleEnum::tryFrom($type);
            $icon = $enumInstance ? $enumInstance->icon() : null;
            return [
                'id' => $schedule->id,
                'title' => 'Order #' . $schedule->sx_ordernumber,
                'start' => $schedule->schedule_date,
                'description' => 'schedule',
                'icon' => $icon,
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
        $this->showModal = true;
        $this->isEdit = true;
        $this->showView = true;
        $this->updatedFormSuffix($schedule->order_number_suffix);
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
        $this->handleDateClick(Carbon::now());
        $this->getTruckData();
        $this->dispatch('calendar-needs-update',  $this->activeWarehouse->title);
    }

    public function handleDateClick($date)
    {
        $date = Carbon::parse($date);
        $this->dateSelected = $date;
        $this->shifts = Shifts::where('whse', $this->activeWarehouse->id)->get();
    }

    public function onDateRangeChanges($start, $end)
    {
        $this->eventStart = Carbon::parse($start);
        $this->eventEnd = Carbon::parse($end);
        $this->getEvents();
        $this->getTruckData();
    }

    public function changeScheduleType($type)
    {
        $this->activeType = $type;
        $this->getEvents();
        $this->dispatch('calendar-type-update', $type != '' ? $this->scheduleOptions[$type] : 'All Services' );

    }

    public function getTruckData()
    {
        $type = $this->activeType;
        $start = $this->eventStart;
        $end = $this->eventEnd;
        $spanText = '';
        $query = ShiftRotation::whereBetween('scheduled_date', [$this->eventStart, $this->eventEnd])
        ->whereHas('shift', function($query) use ($start, $end, $type) {
            $query->where('whse', $this->activeWarehouse->id);
        });

        if($type == 'at_home_maintenance') {
            $query = $query->whereHas('shift', function($query) use ($start, $end, $type) {
                $query->where('type', 'ahm');
            });
            $spanText  = 'AHM';
        }

        if($type == 'delivery' || $type == 'pickup') {
            $query = $query->whereHas('shift', function($query) use ($start, $end, $type) {
                $query->where('type', 'delivery_pickup');
            });
            $spanText = 'P/D';
        }
        if($type == 'setup_install') {
            $query = $query->whereHas('shift', function($query) use ($start, $end, $type) {
                $query->where('type', 'setup_install');
            });
            $spanText = 'S/I';
        }

        $this->truckInfo  = $query->get()

         ->map(function ($truck) {
             $spanText  = '';
           if( $truck->shift->type == 'ahm') {
                $spanText  = 'AHM';
           }
           if( $truck->shift->type == 'delivery_pickup') {
                $spanText  = 'P/D';
           }
            return [
                'id' => $truck->id,
                'scheduled_date' => $truck->scheduled_date,
                'service_type' => $truck->shift->type,
                'truck_name' => $truck->truck->truck_name,
                'spanText' => $spanText. ' '.$truck->zone->name,
                'whse' => $truck->shift->whse,
            ];
        })->toArray();

    }

}
