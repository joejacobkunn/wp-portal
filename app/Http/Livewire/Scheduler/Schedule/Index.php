<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleForm;
use App\Http\Livewire\Scheduler\Schedule\Forms\TruckScheduleForm;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Truck;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zones;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;

class Index extends Component
{
    use LivewireAlert;

    public ScheduleForm $form;

    public TruckScheduleForm $truckScheduleForm;

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
    public $eventStart;
    public $eventEnd;
    public $activeType;
    public $truckInfo = [];
    public $activeWarehouse;
    public $eventCount;
    public $filteredSchedules = [];
    public $selectedTruck;
    public $showSlotModal = false;
    public $availableZones;
    public $eventsData;

    protected $listeners = [
        'closeModal' => 'closeModal',
        'edit' => 'edit',
        'deleteRecord' => 'delete',
        'typeCheck' => 'typeCheck',
        'closeAddress' => 'closeAddress',
        'setScheduleTimes' => 'setScheduleTimes',
        'closeTimeSlot' => 'closeTimeSlot',
        'closeSlotModal' => 'closeSlotModal',
        'scheduleTypeChange' => 'scheduleTypeChange'
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
        $this->form->calendarInit();

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
        if(is_numeric($value))
        {
            $this->form->getOrderInfo($value);
            $this->dispatch('enable-date-update', enabledDates: $this->form->enabledDates);
        }
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
        $this->form->calendarInit();
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
        $this->dateSelected = $date;
        $date = Carbon::parse($date)->format('Y-m-d');

        $this->eventsData =  Schedule::where('schedule_date', $date)->get();

        $this->availableZones = Zones::whereHas('truckSchedules', function ($query) use ($date) {
            $query->where('schedule_date', $date);
        })->get();

        if ($this->availableZones->isNotEmpty()) {
            $this->filteredSchedules = $this->getTrucks($this->availableZones->first()->id);
        } else {
            $this->filteredSchedules = [];
        }

        if($this->filteredSchedules) {
           $this->showTruckData( $this->filteredSchedules['0']['id']);
           return;
       }
       $this->reset(['selectedTruck']);
       $this->truckScheduleForm->reset();
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
        $this->getTruckData();
        $this->dispatch('calendar-type-update', $type != '' ? $this->scheduleOptions[$type] : 'All Services' );

    }

    public function getTrucks($zoneId)
    {
        $date = $this->dateSelected;
        $filteredData = array_filter($this->truckInfo, function ($item) use ($zoneId, $date) {
            return $item['zone_id'] === $zoneId && $item['schedule_date'] === $date;
        });
        $filteredData = array_values($filteredData);
        $uniqueData = array_values(array_reduce($filteredData, function ($carry, $item) {
            if (!isset($carry[$item['truck_id']])) {
                $carry[$item['truck_id']] = $item;
            }
            return $carry;
        }, []));
        return $uniqueData;
    }

    public function getTruckData()
    {
        $type = $this->activeType;
        $start = $this->eventStart;
        $end = $this->eventEnd;
        $spanText = '';
        $query = TruckSchedule::whereBetween('schedule_date', [$this->eventStart, $this->eventEnd])
                ->whereHas('truck', function($query) use ($start, $end, $type) {
                    $query->where('whse', $this->activeWarehouse->id);
                });
        if($type == 'at_home_maintenance') {
            $query = $query->whereHas('truck', function($query) use ($start, $end, $type) {
                $query->where('service_type', 'AHM');
            });
        }

        if($type == 'delivery' || $type == 'pickup') {
            $query = $query->whereHas('truck', function($query) use ($start, $end, $type) {
                $query->where('service_type', 'Delivery / Pickup');
            });
        }
        if($type == 'setup_install') {
            $query = $query->whereHas('truck', function($query) use ($start, $end, $type) {
                $query->where('service_type', 'setup_install');
            });
        }

        $this->truckInfo  = $query->get()
        ->map(function ($truck) {
            return [
                'id' => $truck->id,
                'schedule_date' => $truck->schedule_date,
                'service_type' => $truck->truck->service_type,
                'truck_name' => $truck->truck->truck_name,
                'truck_id' => $truck->truck->id,
                'vin_number' => $truck->truck->vin_number,
                'spanText' => $truck->zone->name. ' - '.$truck->truck->truck_name,
                'whse' => $truck->truck->whse,
                'zone' => $truck->zone->name,
                'zone_id' => $truck->zone->id,
            ];
        })->toArray();
    }


    public function showTruckData($shiftId)
    {
        $this->selectedTruck = TruckSchedule::find($shiftId);
        $this->truckScheduleForm->init($this->selectedTruck);
    }

    public function showSlotModalForm()
    {
        $this->showSlotModal = true;
    }

    public function closeSlotModal()
    {
        $this->showSlotModal = false;
    }

    public function updateSlot()
    {
        $this->truckScheduleForm->update();
        $this->alert('success', 'Slots Updated');
        $this->selectedTruck = $this->selectedTruck->fresh();
        $this->closeSlotModal();
    }
    public function updateFormScheduleDate($date)
    {
        $this->form->schedule_date = Carbon::parse($date)->format('Y-m-d');
        $this->form->getTruckSchedules();
        $this->form->reset('shiftMsg');
        $this->form->schedule_time = null;
    }

    public function selectSlot($scheduleId)
    {
        $schedule = TruckSchedule::find($scheduleId);
        $this->form->schedule_time = $schedule->id;
        $this->form->shiftMsg = 'service is scheduled for '
            .$this->form->schedule_date.' between '.$schedule->start_time. ' - '.$schedule->end_time;
        $this->resetValidation(['form.schedule_time']);
    }

    public function scheduleTypeChange($field, $value)
    {
        $this->form->scheduleType = $value;
        if($value == 'one_year') {
            $date = Carbon::now()->addYear()->format('Y-m-d');
        }
        if($value == 'next_avail') {
            $date = isset($this->form->enabledDates[0]) ? $this->form->enabledDates[0] : Carbon::now()->format('Y-m-d');
        }
        $this->dispatch('set-current-date', activeDay: $date);
    }
}
