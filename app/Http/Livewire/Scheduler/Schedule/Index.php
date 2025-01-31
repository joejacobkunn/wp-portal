<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleForm;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleViewForm;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\Warehouse;
use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Truck;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\Zones;
use App\Models\SRO\RepairOrders;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Str;

class Index extends Component
{
    use LivewireAlert;

    public ScheduleForm $form;
    public ScheduleViewForm $viewForm;

    public $showModal;
    public $schedules;
    public $isEdit;
    public $showView;
    public $addressModal;
    public $orderInfoStrng;
    public $scheduleOptions;
    public $dateSelected;
    public $holidays;
    public $eventStart;
    public $eventEnd;
    public $activeType;
    public $activeZone = [];
    public $truckInfo = [];
    public $filteredSchedules = [];
    public $showSearchModal = false;
    public $availableZones;
    public $eventsData;
    public $showTypeLoader =false;
    public $activeWarehouseId;
    public $searchKey;
    public $searchData;
    public $scheduledLineItem;
    public $sro_number;
    public $sro_verified = false;
    public $sro_response;
    public $serviceAddressModal = false;

    protected $listeners = [
        'closeModal' => 'closeModal',
        'edit' => 'edit',
        'deleteRecord' => 'delete',
        'typeCheck' => 'typeCheck',
        'closeAddress' => 'closeAddress',
        'setScheduleTimes' => 'setScheduleTimes',
        'closeTimeSlot' => 'closeTimeSlot',
        'closeSearchModal' => 'closeSearchModal',
        'scheduleTypeChange' => 'scheduleTypeChange',
        'scheduleTypeDispatch' => 'scheduleTypeDispatch',
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

        if (Auth::user()->office_location) {
            $activeWarehouse = $this->warehouses->firstWhere('title', Auth::user()->office_location);

            $this->setActiveWarehouse($activeWarehouse ? $activeWarehouse->id : $this->warehouses->first()->id);
        } else {
            $this->setActiveWarehouse($this->warehouses->first()->id);
        }

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

       $this->handleDateClick(Carbon::now());
    }

    public function getWarehousesProperty()
    {
        $data = Warehouse::select(['id', 'short', 'title'])
            ->where('cono', 10)
            ->orderBy('title', 'asc')
            ->get();

        return $data;
    }

    public function getActiveWarehouseProperty()
    {
        return $this->warehouses->find($this->activeWarehouseId);
    }

    public function setActiveWarehouse($warehouseId)
    {
        $this->activeWarehouseId = $warehouseId;
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
            $response = $this->form->update();

        } else {
            $this->authorize('store', Schedule::class);
            $response = $this->form->store();
        }

        $this->alert($response['class'], $response['message']);
        $this->handleEventClick($response['schedule']);
    }

    public function updatedFormSuffix($value)
    {
        if(is_numeric($value))
        {
            $this->form->getOrderInfo($value, $this->activeWarehouse->short);
            $this->dispatch('enable-date-update', enabledDates: $this->form->enabledDates);
        }
    }

    public function updatedFormSxOrdernumber($value)
    {
        $this->form->suffix = null;
        $this->form->reset([
            'orderInfo',
            'zipcodeInfo',
            'scheduleType',
            'schedule_date',
            'shiftMsg',
            'schedule_time',
            'line_items'
        ]);
    }

    public function updatedSroNumber($value)
    {
        $this->sro_response = [];
        $this->sro_verified = false;

        $this->sro_response = strlen($value) > 6 ? $this->getSROInfo($value) : [];

    }

    public function linkSRO()
    {
        $this->form->linkSRONumber($this->sro_number);
        $this->alert('success', 'SRO number successfully linked');
    }

    public function typeCheck($field, $value)
    {
        $this->form->type = $value;
        $this->form->checkServiceValidity($value);
    }

    public function getEvents()
    {

        $query = $this->getSchedules();
        $this->schedules =  $query->get()
        ->map(function ($schedule) {
            $type = Str::title(str_replace('_', ' ', $schedule->type));
            $enumInstance = ScheduleEnum::tryFrom($type);
            $icon = $enumInstance ? $enumInstance->icon() : null;
            return [
                'id' => $schedule->id,
                'title' => 'Order #' . $schedule->sx_ordernumber,
                'start' => $schedule->schedule_date->format('Y-m-d'),
                'description' => 'schedule',
                'color' => $schedule->status_color,
                'icon' => $icon,
            ];
        });
    }


    public function handleEventClick(Schedule $schedule)
    {
        $this->form->init($schedule);
        $this->viewForm->init($schedule);
        $this->orderInfoStrng = uniqid();
        $this->scheduledLineItem = Product::whereRaw('account_id = ? and LOWER(`prod`) = ? LIMIT 1',[1,strtolower($schedule->line_items)])->get()->toArray();
        $this->showModal = true;
        $this->isEdit = true;
        $this->showView = true;
        $this->sro_number = $schedule->sro_number;
        if($this->sro_number) {
            $this->sro_verified = true;
            $this->sro_response = $this->getSROInfo($this->sro_number);
        }
        if($this->showSearchModal) {
            $this->closeSearchModal();
            $this->dispatch('jump-to-date', activeDay: $schedule->schedule_date->format('Y-m-d'));
        }
        $this->dispatch('modalContentLoaded');
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

    public function updateAddress()
    {
        $this->form->setAddress(true);
        $this->closeServiceAddressModal();

    }

    public function changeWarehouse($wsheID)
    {
        $this->setActiveWarehouse($wsheID);
        $this->getEvents();
        $this->handleDateClick(Carbon::now());
        $this->getTruckData();
        $this->dispatch('calendar-needs-update',  $this->activeWarehouse->title);
    }

    public function handleDateClick($date)
    {
        $this->dateSelected = $date;
        $date = Carbon::parse($date)->format('Y-m-d');

        $query = $this->getSchedules();

        $this->eventsData = $query->where('schedule_Date', $date)
        ->get()
        ->map(function($schedule){
            return [
                'id' => $schedule->id,
                'type' => $schedule->type,
                'sx_ordernumber' => $schedule->sx_ordernumber,
                'schedule_date' => $schedule->schedule_date,
                'status' => $schedule->status,
                'order_number_suffix' => $schedule->order_number_suffix,
                'customer_name' => $schedule->order->customer->name,
                'sx_customer_number' => $schedule->order->customer->sx_customer_number,
                'shipping_info' => $schedule->order->shipping_info,
                'truckName' => $schedule->truckSchedule->truck->truck_name,
                'zone' => $schedule->truckSchedule->zone->name,
                'status_color' => $schedule->status_color_class,
            ];
        })
        ->toArray();
        $this->filteredSchedules = $this->getTrucks();
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

    public function getTrucks()
    {
        $date = Carbon::parse($this->dateSelected)->format('Y-m-d');
        $filteredData = array_filter($this->truckInfo, function ($item) use ( $date) {
            return $item['schedule_date'] === $date;
        });
        $filteredData = array_values($filteredData);
        return $filteredData;
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

        if(!empty($this->activeZone)) {
            $query = $query->where('zone_id', $this->activeZone['id']);
        }
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
                'spanText' => $truck->zone?->name. ' - '.$truck->truck->truck_name,
                'whse' => $truck->truck->whse,
                'zone' => $truck->zone?->name,
                'zone_id' => $truck->zone_id,
                'start_time' => $truck->start_time,
                'end_time' => $truck->end_time,
                'slots' => $truck->slots,
                'scheduled_count' => $truck->schedule_count,
            ];
        })->toArray();
    }

    public function showSearchModalForm()
    {
        $this->showSearchModal = true;
    }

    public function closeSearchModal()
    {
        $this->showSearchModal = false;
        $this->resetValidation('searchKey');
        $this->reset(['searchKey', 'searchData']);
    }

    public function updateFormScheduleDate($date)
    {
        $this->form->schedule_date = Carbon::parse($date)->format('Y-m-d');
        $this->form->getTruckSchedules();
        $this->form->schedule_time = null;
    }

    public function selectSlot($scheduleId)
    {
        $schedule = TruckSchedule::find($scheduleId);
        $this->form->schedule_time = $schedule->id;
        $this->resetValidation(['form.schedule_time']);
    }

    public function scheduleTypeChange($field, $value)
    {
        $this->showTypeLoader = true;
        $this->form->scheduleType = $value;
        $this->dispatch('scheduleTypeDispatch');

    }

    public function scheduleTypeDispatch()
    {
        if($this->form->scheduleType == 'one_year') {
            $date = Carbon::now()->addYear()->format('Y-m-d');
        }

        if($this->form->scheduleType == 'next_avail') {
            $date = isset($this->form->enabledDates[0]) ? $this->form->enabledDates[0] : Carbon::now()->format('Y-m-d');
        }

        $this->form->reset(['schedule_time', 'truckSchedules', 'schedule_date']);
        $this->dispatch('set-current-date', activeDay: $date);
        $this->showTypeLoader = false;
    }

    public function changeZone($zoneId)
    {
        if($zoneId && $zoneId != '') {
            $this->activeZone = Zones::find($zoneId)->toArray();
        } else {
            $this->activeZone = [];
        }

        $this->getEvents();
        $this->getTruckData();
        $this->dispatch('calendar-zone-update', !empty($this->activeZone) ? $this->activeZone['name'] : 'All Zones' );
    }

    public function updatedSearchKey($value)
    {
        $this->searchKey = $value;
        if($this->searchKey == '') {
            $this->addError('searchKey', 'search field can\'t be empty');
            $this->reset('searchData');
            return;
        }
        $this->resetValidation('searchKey');

        $query = Schedule::with([
            'truckSchedule' => function($query) {
                $query->whereNull('deleted_at')
                    ->select('id', 'start_time', 'end_time');
            },
            'order' => function($query) {
                $query->whereNull('deleted_at')
                    ->select('id', 'order_number', 'sx_customer_number', 'shipping_info')
                    ->with(['customer' => function($query) {
                        $query->whereNull('deleted_at')
                            ->select('id', 'name', 'email', 'phone', 'sx_customer_number');
                    }]);
            }
         ])
         ->join('truck_schedules', 'truck_schedules.id', '=', 'schedules.truck_schedule_id')
         ->whereNull('truck_schedules.deleted_at')
         ->join('orders', 'orders.order_number', '=', 'schedules.sx_ordernumber')
         ->whereNull('orders.deleted_at')
         ->join('customers', 'orders.sx_customer_number', '=', 'customers.sx_customer_number')
         ->whereNull('customers.deleted_at')
         ->select(
            'schedules.id',
            'schedules.schedule_date',
            'schedules.sx_ordernumber',
            'schedules.type',
            'schedules.order_number_suffix',
            'schedules.sx_ordernumber',
            'schedules.truck_schedule_id'
         )
         ->groupBy('schedules.id')
         ->limit(100);


        if (is_numeric($this->searchKey)) {
            $length = strlen($this->searchKey);
            if ($length === 8) {
                $query->where('schedules.sx_ordernumber', $this->searchKey);
            } elseif ($length === 10) {
                $query->where('customers.phone', $value);
            } else {
                $this->searchData = [];
                return;
            }
        } elseif (filter_var($this->searchKey, FILTER_VALIDATE_EMAIL)) {
            $query->where('customers.email',  $value);
        } elseif (Str::length($this->searchKey) >= 4) {
            $query->where('customers.name', 'like', $value . '%');
        } else {
            $this->searchData = [];
            return;
        }

        $this->searchData = $query->get()->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'schedule_date' => optional($schedule->schedule_date)->toFormattedDayDateString(),
                'schedule_time' => optional($schedule->truckSchedule)->start_time
                                   . ' - ' . optional($schedule->truckSchedule)->end_time,
                'sx_ordernumber' => $schedule->sx_ordernumber,
                'order_number_suffix' => $schedule->order_number_suffix,
                'type' => $schedule->type,
                'customer' => optional($schedule->order->customer)->name,
                'sx_customer_number' => optional($schedule->order->customer)->sx_customer_number,
                'shipping_info' => optional($schedule->order)->shipping_info,
            ];
        });


    }

    public function getSchedules()
    {
        $whse = $this->activeWarehouse;
        $query = Schedule::with('order.customer');
        if($this->activeType && $this->activeType != '') {
            $query->where('type', $this->activeType);
        }
        $query->whereBetween('schedule_date', [$this->eventStart, $this->eventEnd])
        ->whereHas('truckSchedule', function ($query) use ($whse) {
            $query->whereIn('truck_id', Truck::where('whse', $whse->id)->pluck('id')->toArray());
        });

        if(!empty($this->activeZone)) {
            $zoneId = $this->activeZone['id'];
            $query = $query->whereHas('truckSchedule', function ($query) use ($zoneId) {
                $query->where('zone_id', $zoneId);
            });
        }

        return $query;
    }

    public function showAddressModal()
    {
        $this->serviceAddressModal = true;
    }

    public function closeServiceAddressModal()
    {
        $this->serviceAddressModal = false;
    }

    public function cancelSchedule()
    {
        $this->viewForm->update();
    }

    public function getSROInfo($sro)
    {
        if(config('sx.mock'))
        {
            $faker = \Faker\Factory::create();
            return [
                'first_name' => $faker->name(),
                'last_name' => $faker->lastName(),
                'address' => $faker->streetAddress(),
                'state' => $faker->state(),
                'city' => $faker->city(),
                'zip' => $faker->postcode(),
                'brand' => 'Toro',
                'model' => 'ghd567df'
            ];
        }else{
            $sro = RepairOrders::select('first_name','last_name', 'address','state', 'city', 'zip', 'brand', 'model')->where('sro_no', $sro)->first();
            if(!empty($sro)) {
                return $sro->toArray();
            }
            return null;
        }
    }
}
