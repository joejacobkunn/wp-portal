<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Enums\Scheduler\ScheduleEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleForm;
use App\Http\Livewire\Scheduler\Schedule\Forms\ScheduleViewForm;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\User;
use App\Models\Core\Warehouse;
use App\Models\Product\Product;
use App\Models\Scheduler\Truck;
use App\Models\Scheduler\Zones;
use App\Models\SRO\RepairOrders;
use App\Models\Scheduler\Schedule;
use App\Exports\Scheduler\OrderScheduleExport;
use App\Models\Scheduler\NotificationTemplate;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Scheduler\TruckSchedule;
use App\Traits\HasTabs;
use Illuminate\Support\Facades\Validator;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert, HasTabs;

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
    public $eventsData = [];
    public $showTypeLoader =false;
    public $activeWarehouseId;
    public $searchKey;
    public $searchData;
    public $scheduledLineItem;
    public $sro_number;
    public $sro_verified = false;
    public $sro_response;
    public $viewScheduleTypeCollapse = false;
    public $serviceAddressModal = false;
    public $showDriverModal = false;
    public $drivers = [];
    public $exportModal = false;
    public $exportFromDate;
    public $exportToDate;
    public $scheduledTruckInfo = [];

    protected $listeners = [
        'closeModal' => 'closeModal',
        'edit' => 'edit',
        'deleteRecord' => 'delete',
        'typeCheck' => 'typeCheck',
        'setScheduleTimes' => 'setScheduleTimes',
        'closeTimeSlot' => 'closeTimeSlot',
        'closeSearchModal' => 'closeSearchModal',
        'scheduleTypeChange' => 'scheduleTypeChange',
        'scheduleTypeDispatch' => 'scheduleTypeDispatch',
        'closeDriverModal' => 'closeDriverModal',
        'closeAddressValidation' => 'closeAddressValidation',
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

    public $tabs = [
        'schedule-comment-tabs' => [
            'active' => 'comments',
            'links' => [
                'comments' => 'Comments',
                'activity' => 'Activity',
            ],
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

        $this->drivers = User::whereIn('title', ['Driver', 'Service Technician'])
        ->where('office_location', $this->activeWarehouse->title)
        ->get()
        ->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'title' => $user->title,
                'name_title' => $user->name.' ('. $user->title . ')'
            ];
        })
        ->sortBy('name')
        ->toArray();

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
        $this->reset([
            'sro_number',
            'sro_verified',
            'sro_response',
            'viewScheduleTypeCollapse'
        ]);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.schedule.index');
    }

    public function submit()
    {

        $this->authorize('store', Schedule::class);
        $response = $this->form->store();
        $type = Str::title(str_replace('_', ' ', $response['schedule']->type));
        $enumInstance = ScheduleEnum::tryFrom($type);
        $icon = $enumInstance ? $enumInstance->icon() : null;
        $event = [
            'id' => $response['schedule']->id,
            'title' => 'Order #' . $response['schedule']->sx_ordernumber,
            'start' => $response['schedule']->schedule_date->format('Y-m-d'),
            'description' => 'schedule',
            'color' => $response['schedule']->status_color,
            'icon' => $icon,
        ];
        $this->alert($response['class'], $response['message']);
        $this->handleEventClick($response['schedule']);
        $this->dispatch('add-event-calendar', newEvent: $event);
    }

    public function updatedFormSuffix($value)
    {
        if(is_numeric($value))
        {
            $this->form->reset([
                'orderInfo',
                'zipcodeInfo',
                'scheduleType',
                'schedule_date',
                'schedule_time',
                'line_item',
                'alertConfig',
                'ServiceStatus'
            ]);
            $this->reset('scheduledTruckInfo');
            $this->validateOnly('form.sx_ordernumber');
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
            'schedule_time',
            'line_item',
            'alertConfig',
            'ServiceStatus'
        ]);
        $this->reset('scheduledTruckInfo');
    }

    public function updatedSroNumber($value)
    {
        $this->sro_response = [];
        $this->sro_verified = false;

        $this->sro_response = strlen($value) > 6 ? $this->getSROInfo($value) : [];

    }

    public function linkSRO()
    {
        $response = $this->form->linkSRONumber($this->sro_number);
        $this->alert($response['class'], $response['message']);
        $this->EventUpdate($response);
    }

    public function typeCheck($field, $value)
    {
        $this->form->type = $value;
        $this->form->checkServiceValidity($value);
        if(!$this->form->ServiceStatus) {
            $this->reset('scheduledTruckInfo');
        }
    }

    public function getEvents()
    {

        $query = $this->getSchedules();
        $this->schedules =  $query
        ->orderByRaw('COALESCE(schedules.travel_prio_number, 9999) asc')
        ->orderByRaw('STR_TO_DATE(schedules.expected_arrival_time, "%h:%i %p") asc')
        ->orderBy('schedules.created_at', 'asc')
        ->get()
        ->map(function ($schedule, $index) {
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
                'sortIndex' => $index
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



    public function updateAddress()
    {
        $this->form->service_address = $this->form->service_address_temp;
        $this->form->updatedAddress();
        $this->closeServiceAddressModal();
    }

    public function changeWarehouse($wsheID)
    {
        $this->setActiveWarehouse($wsheID);
        $this->getEvents();
        $this->getTruckData();
        $this->handleDateClick($this->dateSelected);
        $this->dispatch('calendar-needs-update',  $this->activeWarehouse->title);
    }

    public function handleDateClick($date)
    {
        $this->dateSelected = $date;
        $date = Carbon::parse($date)->format('Y-m-d');

        $query = $this->getSchedules();

        $this->eventsData = $query->where('schedule_Date', $date)
        ->orderByRaw('COALESCE(travel_prio_number, 9999) asc')
        ->orderByRaw('STR_TO_DATE(schedules.expected_arrival_time, "%h:%i %p") asc')
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(function($schedule){
            return [
                'id' => $schedule->id,
                'schedule_id' => $schedule->scheduleId(),
                'type' => $schedule->type,
                'sx_ordernumber' => $schedule->sx_ordernumber,
                'schedule_date' => $schedule->schedule_date,
                'status' => $schedule->status,
                'order_number_suffix' => $schedule->order_number_suffix,
                'customer_name' => $schedule->order->customer?->name,
                'sx_customer_number' => $schedule->order->customer?->sx_customer_number,
                'shipping_info' => $schedule->order->shipping_info,
                'truckName' => $schedule->truckSchedule->truck->truck_name,
                'zone' => $schedule->truckSchedule->zone->name,
                'status_color' => $schedule->status_color_class,
                'expected_time' => $schedule->expected_arrival_time,
                'travel_prio_number' => $schedule->travel_prio_number,
                'truck_schedule_id' => $schedule->truck_schedule_id,
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
        $this->handleDateClick($this->dateSelected);
        $this->dispatch('calendar-type-update', $type != '' ? $this->scheduleOptions[$type] : 'All Services' );

    }

    public function getTrucks()
    {
        $date = Carbon::parse($this->dateSelected)->format('Y-m-d');
        $filteredData = array_filter($this->truckInfo, function ($item) use ( $date) {
            return $item['schedule_date'] === $date;
        });

        $filteredData = array_values($filteredData);
        $events = $this->eventsData;
        $schedules = collect($filteredData)->map(function ($schedule) use ($events) {
            $schedule['events'] = collect($events)
                ->where('truck_schedule_id', $schedule['id'])
                ->values()
                ->toArray();
            return $schedule;
        })->toArray();
        return $schedules;

    }

    public function getTruckData()
    {
        $type = $this->activeType;
        $truckScheduleQuery = TruckSchedule::query()
            ->select([
                'truck_schedules.id',
                'truck_schedules.schedule_date',
                'truck_schedules.start_time',
                'truck_schedules.end_time',
                'truck_schedules.slots',
                'truck_schedules.driver_id',
                'truck_schedules.zone_id',
                'truck_schedules.truck_id',
                'trucks.id as truck_table_id',
                'trucks.service_type',
                'trucks.truck_name',
                'trucks.vin_number',
                'trucks.whse',
                'zones.name as zone_name',
                'users.name as driver_name',
                'users.title as driver_title'
            ])
            ->with('orderSchedule')
            ->join('trucks', 'truck_schedules.truck_id', '=', 'trucks.id')
            ->join('zones', 'truck_schedules.zone_id', '=', 'zones.id')
            ->leftjoin('users', 'truck_schedules.driver_id', '=', 'users.id')
            ->whereNull('truck_schedules.deleted_at')
            ->whereNull('trucks.deleted_at')
            ->whereNull('zones.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereBetween('truck_schedules.schedule_date', [$this->eventStart, $this->eventEnd])
            ->where('trucks.whse', $this->activeWarehouse->id);

        if (!empty($this->activeZone)) {
            $truckScheduleQuery->where('truck_schedules.zone_id', $this->activeZone['id']);
        }

        if ($type == 'at_home_maintenance') {
            $truckScheduleQuery->where('trucks.service_type', 'AHM');
        } elseif ($type == 'delivery' || $type == 'pickup') {
            $truckScheduleQuery->where('trucks.service_type', 'Delivery / Pickup');
        } elseif ($type == 'setup_install') {
            $truckScheduleQuery->where('trucks.service_type', 'setup_install');
        }

        $this->truckInfo = $truckScheduleQuery
            ->orderBy('truck_schedules.created_at', 'asc')
            ->get()
            ->map(function ($truck) {
                return [
                    'id' => $truck->id,
                    'schedule_date' => $truck->schedule_date,
                    'service_type' => $truck->service_type,
                    'truck_name' => $truck->truck_name,
                    'truck_id' => $truck->truck_id,
                    'vin_number' => $truck->vin_number,
                    'spanText' => $truck->zone_name . ' - ' . $truck->truck_name,
                    'whse' => $truck->whse,
                    'zone' => $truck->zone_name,
                    'zone_id' => $truck->zone_id,
                    'start_time' => $truck->start_time,
                    'end_time' => $truck->end_time,
                    'slots' => $truck->slots,
                    'scheduled_count' => $truck->schedule_count,
                    'driver_id' => $truck->driver_id,
                    'driverName' => $truck->driver_name ? $truck->driver_name . ' (' . $truck->driver_title . ')' : null,
                ];
            })->toArray();
        return $this->truckInfo;
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
        $this->scheduledTruckInfo = [
            'truck_name' => $schedule->truck->truck_name,
            'vin_number' => $schedule->truck->vin_number,
            'driver_name' => $schedule->driver?->name,
        ];
        $this->resetValidation(['form.schedule_time']);
    }

    public function scheduleTypeChange($field, $value)
    {
        $this->showTypeLoader = true;
        if(isset($this->form->Schedule)) {
            $this->viewScheduleTypeCollapse = true;
        }
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
        $this->activeZone = [];
        if($zoneId && $zoneId != '') {
            $this->activeZone = Zones::find($zoneId)->toArray();
        }

        $this->getEvents();
        $this->getTruckData();
        $this->handleDateClick($this->dateSelected);
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
        $this->form->service_address_temp = $this->form->service_address;
        $this->serviceAddressModal = true;
    }

    public function closeServiceAddressModal()
    {
        $this->serviceAddressModal = false;
    }

    public function cancelSchedule()
    {
        $this->form->cancelSchedule();
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

    public function cancelConfirm()
    {
        $response = $this->form->unlinkSRO();
        $this->reset([
            'sro_number',
            'sro_verified',
            'sro_response'
        ]);
        $this->alert($response['class'], $response['message']);
        $this->EventUpdate($response);
    }

    public function scheduleDateInitiate()
    {
        $this->viewScheduleTypeCollapse = !$this->viewScheduleTypeCollapse;
    }
    public function getEnabledDates()
    {
        if(empty($this->form->enabledDates)) {
            $this->form->getEnabledDates();
        }
        return $this->form->enabledDates;
    }

    public function save()
    {
        $this->authorize('update', $this->form->schedule);
        $response = $this->form->update();
        $this->viewScheduleTypeCollapse = false;
        $this->EventUpdate($response);
    }

    public function undoCancel()
    {
        $this->authorize('update', $this->form->schedule);
        $response = $this->form->undoCancel();
        $this->EventUpdate($response);
    }
    public function startSchedule()
    {
        $this->authorize('startSchedule', $this->form->schedule);
        $response = $this->form->startSchedule();
        $this->EventUpdate($response);
    }

    public function completeSchedule()
    {
        $this->authorize('update', $this->form->schedule);
        $response = $this->form->completeSchedule();
        $this->EventUpdate($response);
    }

    public function hideScheduleSection()
    {
        $this->viewScheduleTypeCollapse = false;
    }

    public function EventUpdate($response)
    {
        $type = Str::title(str_replace('_', ' ', $response['schedule']->type));
        $enumInstance = ScheduleEnum::tryFrom($type);
        $icon = $enumInstance ? $enumInstance->icon() : null;
        $event = [
            'id' => $response['schedule']->id,
            'title' => 'Order #' . $response['schedule']->sx_ordernumber,
            'start' => $response['schedule']->schedule_date->format('Y-m-d'),
            'description' => 'schedule',
            'color' => $response['schedule']->status_color,
            'icon' => $icon,
        ];
        $this->alert($response['class'], $response['message']);
        $this->dispatch('remove-event-calendar', eventId: $response['schedule']->id);
        $this->dispatch('add-event-calendar', newEvent: $event);
    }

    public function openDriverModal()
    {
        $this->showDriverModal = true;
    }

    public function asignDrivers()
    {
        $validator = Validator::make(
            ['schedules' => $this->filteredSchedules],
            [
                'schedules'                   => 'required|array|min:1',
                'schedules.*.driver_id'       => 'required|exists:users,id',
            ]
        );

        // Check validation
        if ($validator->fails()) {
            $this->addError('asignDrivers', 'Invalid data provided for driver assignment.');
            return;
        }
        $truckSchedules = TruckSchedule::whereIn('id', collect($this->filteredSchedules)->pluck('id'))->get()->keyBy('id');
        foreach ($this->filteredSchedules as $schedule) {
            if (isset($truckSchedules[$schedule['id']])) {
                $truckSchedules[$schedule['id']]->driver_id = $schedule['driver_id'];
                $truckSchedules[$schedule['id']]->save();
                foreach ($this->truckInfo as $key => $truck) {
                    if ($truck['id'] == $schedule['id']) {
                        $this->truckInfo[$key]['driver_id'] = $schedule['driver_id'];
                        $this->truckInfo[$key]['driverName'] = $truckSchedules[$schedule['id']]->driver->name.' ('
                        .$truckSchedules[$schedule['id']]->driver->title.')';
                        break;
                    }
                }
            }
        }
        $date = $this->filteredSchedules[0]['schedule_date'];
        $this->dispatch('calender-remove-driver-span', date: $date);
        $this->closeDriverModal();
    }

    public function closeDriverModal()
    {
        $this->showDriverModal = false;
        $this->filteredSchedules = $this->getTrucks();
    }

    public function showExportModal()
    {
        $this->exportModal = true;
        $this->reset(
            'exportFromDate',
            'exportToDate',
        );
    }

    public function exportSchedules()
    {
        $errorFlag = 0;
        $this->clearValidation();
        if (! $this->exportFromDate) {
            $this->addError('exportFromDate', 'From date field can\'t be empty');
            $errorFlag = 1;
        }

        if (! $this->exportToDate) {
            $this->addError('exportToDate', 'From date field can\'t be empty');
            $errorFlag = 1;
        }

        if (! $errorFlag) {
            $startDate = Carbon::parse($this->exportFromDate);
            $endDate = Carbon::parse($this->exportToDate);

            if ($startDate->diffInDays($endDate) > 365) {
                $this->addError('exportToDate', 'Date range must be under 1 year.');
                $errorFlag = 1;
            }
        }

        if ($errorFlag) {
            return;
        }

        $schedulQuery = $this->getSchedules()
            ->whereBetween('schedule_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $schedules =  $schedulQuery->get()
            ->map(function ($schedule) {
                $type = Str::title(str_replace('_', ' ', $schedule->type));
                $enumInstance = ScheduleEnum::tryFrom($type);

                return [
                    'schedule_id' => $schedule->scheduleId(),
                    'sx_ordernumber' => $schedule->sx_ordernumber,
                    'schedule_date' => $schedule->schedule_date?->format('Y-m-d'),
                    'time_slot' => $schedule->truckSchedule->start_time.' '.$schedule->truckSchedule->end_time,
                    'type' => $enumInstance?->label(),
                    'zone' => $schedule->truckSchedule?->zone?->name,
                    'truckName' => $schedule->truckSchedule?->truck?->truck_name,
                    'status' => $schedule->status,
                    'customer_name' => $schedule->order?->customer?->name,
                    'sx_customer_number' => $schedule->order?->customer?->sx_customer_number,
                    'shipping_address_1' => $schedule->order?->shipping_info['line'] ?? '',
                    'shipping_address_2' => $schedule->order?->shipping_info['line2'] ?? '',
                    'shipping_city' => $schedule->order?->shipping_info['city'] ?? '',
                    'shipping_state' => $schedule->order?->shipping_info['state'] ?? '',
                    'shipping_zip' => $schedule->order?->shipping_info['zip'] ?? '',
                ];
            })
            ->toArray();
        $this->alert('info', 'Initializing Export!');
        $this->exportModal = false;

        return Excel::download(
            new OrderScheduleExport($schedules),
            'Schedule Report '. $startDate->format('d-M-Y') . ' to '. $endDate->format('d-M-Y').'.csv'
        );
    }

    public function updatedFormServiceAddress($value)
    {
        $this->form->service_address = $value;
        $this->form->updatedAddress();
    }

    public function closeAddressValidation()
    {
        $this->useCurrentAddress();
    }

    public function fixAddress()
    {
        $this->form->showAddressBox = true;
    }

    public function useRecommended()
    {
        $this->form->serviceZip = $this->form->extractZipCode($this->form->recommendedAddress);
        $this->form->validateAddress($this->form->recommendedAddress, $this->form->serviceZip);
    }
    public function useCurrentAddress()
    {
        $this->form->serviceZip = $this->form->extractZipCode($this->form->service_address);
        $this->form->showAddressModal = false;
        $this->form->reset([
            'recommendedAddress',
            'showAddressModal',
            'showAddressBox'
        ]);
        $this->form->checkZipcode();
    }

}
