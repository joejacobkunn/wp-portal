<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Enums\Scheduler\ScheduleEnum;
use App\Enums\Scheduler\ScheduleStatusEnum;
use App\Enums\Scheduler\ScheduleTypeEnum;
use App\Http\Livewire\Component\Component;
use App\Models\Core\CalendarHoliday;
use App\Models\Core\User;
use App\Models\Core\Warehouse;
use App\Models\Product\Product;
use App\Models\Scheduler\Truck;
use App\Models\Scheduler\Zones;
use App\Models\SRO\RepairOrders;
use App\Models\Scheduler\Schedule;
use App\Exports\Scheduler\OrderScheduleExport;
use App\Http\Livewire\Scheduler\Schedule\Forms\AnnouncementForm;
use App\Models\Product\Category;
use App\Models\Scheduler\Announcement;
use App\Models\Scheduler\NotificationTemplate;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Scheduler\TruckSchedule;
use App\Models\Scheduler\TruckScheduleReturn;
use App\Traits\HasTabs;
use Illuminate\Support\Facades\Validator;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert, HasTabs;

    public AnnouncementForm $announcementForm;
    public $showModal;
    public $schedules;
    public $showView;
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
    public $activeWarehouseId;
    public $searchKey;
    public $searchData;
    public $scheduledLineItem;
    public $showDriverModal = false;
    public $drivers = [];
    public $exportModal = false;
    public $exportFromDate;
    public $exportToDate;
    public $announceModal;
    public $driverSkills;
    public $selectedType;
    public $selectedSchedule;
    public $truckReturnInfo;
    public $selectedScheduleId;
    public $searchZoneKey;
    public $activeSearchKey = 'schedule';
    public $searchZoneData;
    public $cargoSorting = false;
    public $cargoItems;
    public $cargoError =[];

    protected $queryString = [
        'selectedScheduleId' => ['except' => '*', 'as' => 'id'],
        'activeWarehouseId' => ['except' => '', 'as' => 'whse'],
    ];
    protected $listeners = [
        'closeModal' => 'closeModal',
        'edit' => 'edit',
        'deleteRecord' => 'delete',
        'setScheduleTimes' => 'setScheduleTimes',
        'closeTimeSlot' => 'closeTimeSlot',
        'closeSearchModal' => 'closeSearchModal',
        'closeDriverModal' => 'closeDriverModal',
        'fetchDriverSkills' => 'fetchDriverSkills',
        'closeAnnouncementModal' => 'closeAnnouncementModal',
        'attchQueryParam' => 'attchQueryParam',
    ];

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

    public function getAnnouncementsProperty()
    {
        return Announcement::where('whse', $this->activeWarehouse->short)->select(['id', 'message'])->get()->toArray();
    }

    public function mount()
    {
        $this->authorize('viewAny', Schedule::class);
        $this->orderInfoStrng = uniqid();

        $this->scheduleOptions = collect(ScheduleEnum::cases())
            ->mapWithKeys(fn($case) => [$case->name => $case->icon().' '.$case->label()])
            ->toArray();

        if (!$this->activeWarehouseId) {
            $activeWarehouse = $this->warehouses->firstWhere('title', Auth::user()->office_location) ?? $this->warehouses->first();
            $this->activeWarehouseId = $activeWarehouse->id;
        }
        $this->setActiveWarehouse($this->activeWarehouseId);

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

        if($this->selectedScheduleId) {
            $schedule = Schedule::find($this->selectedScheduleId);
            if($schedule) {
                $this->handleEventClick($schedule);
            }
        }

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
        $this->selectedType = $type;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showView = false;
        $this->resetValidation();
        $this->reset('selectedScheduleId');
        $this->handleDateClick($this->dateSelected);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.schedule.index');
    }

    public function getEvents()
    {

        $query = $this->getSchedules();
        $this->schedules =  $query
        ->orderByRaw("CASE
        WHEN expected_arrival_time = '' THEN 1
        WHEN expected_arrival_time IS NULL THEN 1
        ELSE 0 END, expected_arrival_time ASC")
        ->orderBy('schedules.created_at', 'asc')
        ->get()
        ->map(function ($schedule, $index) {
            $enumInstance = ScheduleEnum::tryFrom($schedule->type);
            $icon = $enumInstance ? $enumInstance->icon() : null;
            $color = $schedule->status_color;
            if($schedule->status == 'scheduled' && $schedule->sro_number != null) {
                $color = '#9E2EC9';
            }
            return [
                'id' => $schedule->id,
                'title' => 'Order #' . $schedule->sx_ordernumber,
                'start' => $schedule->schedule_date->format('Y-m-d'),
                'description' => 'schedule',
                'color' => $color,
                'icon' => $icon,
                'sortIndex' => $index
            ];
        });
    }


    public function handleEventClick(Schedule $schedule)
    {
        $this->selectedSchedule = $schedule;
        $this->selectedScheduleId = $this->selectedSchedule->id;
        $this->orderInfoStrng = uniqid();
        $this->scheduledLineItem = Product::whereRaw('account_id = ? and LOWER(`prod`) = ? LIMIT 1',[1,strtolower($schedule->line_items)])->get()->toArray();
        $this->showModal = true;
        $this->showView = true;
        $this->selectedType = $schedule->type;

        if($this->showSearchModal) {
            $this->closeSearchModal();
            $this->dispatch('jump-to-date', activeDay: $schedule->schedule_date->format('Y-m-d'));
        }
        $this->dispatch('modalContentLoaded');
    }

    public function changeWarehouse($wsheID)
    {
        $this->setActiveWarehouse($wsheID);
        $this->getEvents();
        $this->getTruckData();
        $this->handleDateClick($this->dateSelected);
        $this->dispatch('calendar-needs-update',  $this->activeWarehouse->title);
        $this->dispatch('modalContentLoaded');
    }

    public function handleDateClick($date)
    {
        $this->dateSelected = $date;
        $date = Carbon::parse($date)->format('Y-m-d');

        $query = $this->getSchedules();

        $this->eventsData = $query->where('schedule_Date', $date)
        ->orderByRaw('COALESCE(travel_prio_number, 9999) asc')
        ->orderBy('schedules.expected_arrival_time', 'asc')
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
                'shipping_info' => $schedule->service_address,
                'truckName' => $schedule->truckSchedule->truck->truck_name,
                'zone' => $schedule->truckSchedule->zone->name,
                'status_color' => $schedule->status_color_class,
                'expected_time' => $schedule->expected_arrival_time,
                'travel_prio_number' => $schedule->travel_prio_number,
                'truck_schedule_id' => $schedule->truck_schedule_id,
                'latest_comment' => $schedule->comments->last(),
                'service_address' => $schedule->service_address,
                'line_item' => $schedule->line_item
            ];
        })
        ->toArray();
        $this->filteredSchedules = $this->getTrucks();

        $this->filteredSchedules =  collect($this->filteredSchedules)->sortBy(fn($item) => strtotime($item['start_time']))->values()->all();
        $this->truckReturnInfo = TruckScheduleReturn::where('schedule_date', $date)
        ->where('whse', $this->activeWarehouse->short)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'schedule_date' => $item->schedule_date,
                'expected_arrival_time' => $item->expected_arrival_time,
                'distance' => $item->distance,
                'warehouse_name' => $item->warehouse->title ?? null,
                'warehouse_address' => $item->warehouse->address ?? null,
                'truck_name' => $item->truck->truck_name ?? null,
                'schedule_id' => $item->schedule_id ?? null,
            ];
        })
        ->toArray();
        foreach($this->filteredSchedules as $key =>$truckSchedule) {
            $items = [];
            if($truckSchedule['service_type'] == ScheduleTypeEnum::pickup_delivery->value) {
                $truckScheduleInfo = TruckSchedule::with(['orderSchedule.order', 'truck'])->find($truckSchedule['id']);
                if(!$truckScheduleInfo || count($truckSchedule['events']) <= 0) {
                    continue;
                }

                $cargoItems = $truckScheduleInfo->orderSchedule
                ->map(function ($schedule) {
                    return [
                        'line_item' => $schedule->line_item,
                        'order_line_items' => $schedule->order->line_items ?? [],
                    ];
                })
                ->toArray();
                $lineItems = [];
                $prodcats = collect($cargoItems)
                ->flatMap(fn($cargo) => array_map('strtoupper', array_column($cargo['order_line_items']['line_items'] ?? [], 'prodcat')))
                ->unique()
                ->filter()
                ->values();
                $categories = Category::with('cargoConfigurator')
                ->whereIn('name', $prodcats)
                ->get()
                ->keyBy('name');
                foreach ($cargoItems as $cargo) {
                    foreach ($cargo['line_item'] as $prod => $desc) {
                        // find the matching line item by shipprod
                        $matched = collect($cargo['order_line_items']['line_items'] ?? [])
                            ->firstWhere('shipprod', $prod);
                            $prodcat = strtoupper($matched['prodcat'] ?? '');

                            $category = $categories[$prodcat] ?? null;

                            $lineItems[] = [
                                'prod' => $prod,
                                'desc' => $desc,
                                'prodcat' => $prodcat ?: null,
                                'height' => $category?->cargoConfigurator?->height,
                                'length' => $category?->cargoConfigurator?->length,
                                'width' => $category?->cargoConfigurator?->width,
                                'area' => $category?->cargoConfigurator?->width * $category?->cargoConfigurator?->length,
                            ];
                    }
                }
                $truckArea = $truckScheduleInfo->truck->length * $truckScheduleInfo->truck->width;
                $totalUsedArea = collect($lineItems)->sum('area');
                $usedAreaPercentage = $truckArea > 0 ? ($totalUsedArea / $truckArea) * 100 : 0;
                $usedAreaPercentage = round($usedAreaPercentage, 2);
                $this->filteredSchedules[$key]['totalAreaUsed'] = $usedAreaPercentage;
            }
        }
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
        $this->dispatch('modalContentLoaded');
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
                'trucks.warehouse_short',
                'zones.name as zone_name',
                'users.name as driver_name',
                'users.title as driver_title',
                'user_skills.skills as driver_skills',
                'trucks.height as truck_height',
                'trucks.length as truck_length',
                'trucks.width as truck_width'
            ])
            ->with('orderSchedule')
            ->join('trucks', 'truck_schedules.truck_id', '=', 'trucks.id')
            ->join('zones', 'truck_schedules.zone_id', '=', 'zones.id')
            ->leftjoin('users', 'truck_schedules.driver_id', '=', 'users.id')
            ->leftjoin('user_skills', 'users.id', '=', 'user_skills.user_id')
            ->whereNull('truck_schedules.deleted_at')
            ->whereNull('trucks.deleted_at')
            ->whereNull('zones.deleted_at')
            ->whereNull('users.deleted_at')
            ->whereNull('user_skills.deleted_at')
            ->whereBetween('truck_schedules.schedule_date', [$this->eventStart, $this->eventEnd])
            ->where('trucks.warehouse_short', $this->activeWarehouse->short);

        if (!empty($this->activeZone)) {
            $truckScheduleQuery->where('truck_schedules.zone_id', $this->activeZone['id']);
        }

        $typeArray = [];

        switch ($type) {
            case ScheduleEnum::at_home_maintenance->value:
                $truckScheduleQuery->where('trucks.service_type', $type);
                break;

            case ScheduleEnum::delivery->value:
                $truckScheduleQuery->where('truck_schedules.is_delivery', true);
                break;

            case ScheduleEnum::pickup->value:
                $truckScheduleQuery->where('truck_schedules.is_pickup', true);
                break;

            case ScheduleEnum::setup_install->value:
                $truckScheduleQuery->where('trucks.service_type', 'setup_install');
                break;

            case '':
                $typeArray[] = ScheduleTypeEnum::at_home_maintenance->value;

                if (Auth::user()->can('scheduler.override')) {
                    $typeArray[] = ScheduleTypeEnum::pickup_delivery->value;
                }

                $truckScheduleQuery->whereIn('trucks.service_type', $typeArray);
                break;
        }

        $this->truckInfo = $truckScheduleQuery
            ->orderBy('truck_schedules.created_at', 'asc')
            ->get()
            ->map(function ($truck) {
                return [
                    'id' => $truck->id,
                    'schedule_date' => $truck->schedule_date,
                    'service_type' => $truck->service_type,
                    'schedule_type' => $truck->scheduleType(),
                    'truck_name' => $truck->truck_name,
                    'truck_id' => $truck->truck_id,
                    'vin_number' => $truck->vin_number,
                    'spanText' => $truck->zone_name . ' - ' . $truck->truck_name,
                    'zone' => $truck->zone_name,
                    'zone_id' => $truck->zone_id,
                    'start_time' => $truck->start_time,
                    'end_time' => $truck->end_time,
                    'slots' => $truck->slots,
                    'scheduled_count' => $truck->schedule_count,
                    'driver_id' => $truck->driver_id,
                    'driver_skills' => explode(",", $truck->driver_skills),
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
        $this->resetValidation(['searchKey', 'searchZoneKey']);
        $this->reset(['searchKey', 'searchData', 'searchZoneData', 'searchZoneKey', 'activeSearchKey']);
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
        $this->dispatch('modalContentLoaded');

    }

    public function updatedSearchZoneKey($value)
    {
        $this->activeSearchKey = 'zone';
        $this->searchZoneKey = $value;
        $zipcodePattern = "/^\d{5}$/";
        if ($this->searchZoneKey == '') {
            $this->addError('searchZoneKey', 'Search field can\'t be empty');
            $this->reset('searchZoneData');
            return;
        }

        if (!preg_match($zipcodePattern, $this->searchZoneKey)) {
            $this->addError('searchZoneKey', 'Search key must be a 5-digit number');
            $this->reset('searchZoneData');
            return;
        }
        $this->resetValidation('searchZoneKey');
        $this->searchZoneData = Zones::join('zipcode_zone', 'zones.id', '=', 'zipcode_zone.zone_id')
        ->join('scheduler_zipcodes', 'scheduler_zipcodes.id', '=', 'zipcode_zone.scheduler_zipcode_id')
        ->where('scheduler_zipcodes.zip_code', $this->searchZoneKey)
        ->whereNull('zones.deleted_at')
        ->whereNull('scheduler_zipcodes.deleted_at')
        ->select(['zones.name', 'zones.id'])
        ->distinct()
        ->get()
        ->toArray();
    }

    public function updatedSearchKey($value)
    {
        $this->searchKey = $value;
        $this->activeSearchKey = 'schedule';
        $ScheduleIDpattern = "/^[A-Za-z]\d{7,}$/";
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
        } elseif(preg_match($ScheduleIDpattern, $this->searchKey)){
            $tempId =  (int) substr($this->searchKey, 1);
            $id = $tempId - 1000000;
            $query->where('schedules.id', $id);

        } elseif (filter_var($this->searchKey, FILTER_VALIDATE_EMAIL)) {
            $query->where('customers.email',  $value);
        } elseif (preg_match('/^\d{6}-\d{2}$/', $value)) {
            $query->where('schedules.sro_number', $value);
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
        if($this->activeType == "") {
            $typeArray = [ScheduleEnum::at_home_maintenance->value];
            if(Auth::user()->can('scheduler.override')) {
                $typeArray = array_merge($typeArray, [
                    ScheduleEnum::delivery->value,
                    ScheduleEnum::pickup->value,
                ]);
            }
            $query->whereIn('type', $typeArray);
        } else {
            $query->where('type', $this->activeType);
        }
        $query->whereBetween('schedule_date', [$this->eventStart, $this->eventEnd])
        ->where('whse', $whse->short);

        if(!empty($this->activeZone)) {
            $zoneId = $this->activeZone['id'];
            $query = $query->whereHas('truckSchedule', function ($query) use ($zoneId) {
                $query->where('zone_id', $zoneId);
            });
        }

        return $query;
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
        $this->dispatch('calender-remove-driver-warning', date: $date);
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
        $schedulQuery = Schedule::with('order.customer')->where('whse', $this->activeWarehouse->short);

        if($this->activeType && $this->activeType != '') {
            $schedulQuery = $schedulQuery->where('type', $this->activeType);
        }

        if(!empty($this->activeZone)) {
            $zoneId = $this->activeZone['id'];
            $schedulQuery = $schedulQuery->whereHas('truckSchedule', function ($query) use ($zoneId) {
                $query->where('zone_id', $zoneId);
            });
        }
        $schedulQuery = $schedulQuery->whereBetween('schedule_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $schedulQuery = $schedulQuery->with([
            'latestComment',
            'user',
            'confirmedUser',
            'cancelledUser',
            'completedUser',
            'startedUser',
            'sroLinkedUser'
        ]);
        if(!config('sx.mock')) {
            $schedulQuery = $schedulQuery->with('repairOrder');
        }

        $schedules =  $schedulQuery->get()
            ->map(function ($schedule) {
                $enumInstance = ScheduleEnum::tryFrom($schedule->type);
                return [
                    'schedule_id' => $schedule->scheduleId(),
                    'sx_ordernumber' => $schedule->sx_ordernumber,
                    'schedule_date' => $schedule->schedule_date?->format('Y-m-d'),
                    'time_slot' => $schedule->truckSchedule->start_time.' '.$schedule->truckSchedule->end_time,
                    'type' => $enumInstance?->label(),
                    'zone' => $schedule->truckSchedule?->zone?->name,
                    'truckName' => $schedule->truckSchedule?->truck?->truck_name,
                    'status' => ScheduleStatusEnum::tryFrom($schedule->status)->label(),
                    'customer_name' => $schedule->order?->customer?->name,
                    'sx_customer_number' => $schedule->order?->customer?->sx_customer_number,
                    'shipping_address_1' => $schedule->order?->shipping_info['line'] ?? '',
                    'shipping_address_2' => $schedule->order?->shipping_info['line2'] ?? '',
                    'shipping_city' => $schedule->order?->shipping_info['city'] ?? '',
                    'shipping_state' => $schedule->order?->shipping_info['state'] ?? '',
                    'shipping_zip' => $schedule->order?->shipping_info['zip'] ?? '',
                    'sro_sx_number' => !config('sx.mock') ? $schedule->repairOrder?->sx_repair_order_no ?? '' : '',
                    'sro_status' => !config('sx.mock') ? $schedule->repairOrder?->status ?? '' : '',
                    'equipment' => !config('sx.mock') ? $schedule->repairOrder?->brand ?? '' .$schedule->repairOrder?->model ?? '' : '' ,
                    'serial_number' => $schedule->serial_no ?? '',
                    'notes' => $schedule->latestComment->comment ?? '',
                    'eta' => $schedule->expected_arrival_time ?  Carbon::parse($schedule->expected_arrival_time)->format('h:i A') : '',
                    'created_by' => $schedule->user?->name ?? '',
                    'created_at' => $schedule->created_at->format('d-m-Y H:i A') ?? '',
                    'parts_ready_user' => $schedule->confirmedUser->name ?? '',
                    'parts_ready_timestamp' => $schedule->confirmed_at ? Carbon::parse($schedule->confirmed_at)->format('d-m-Y h:i A') : '',
                    'completed_user' => $schedule->completedUser->name ?? '',
                    'completed_at' => $schedule->completed_at ?? '',
                    'cancelled_by' => $schedule->cancelledUser->name ?? '',
                    'cancelled_at' => $schedule->cancelled_at ? Carbon::parse($schedule->cancelled_at)->format('d-m-Y h:i A') : '',
                    'sro_linked_by' => $schedule->sroLinkedUser->name ?? '',
                    'sro_linked_at' => $schedule->sro_linked_at ? Carbon::parse($schedule->sro_linked_at)->format('d-m-Y h:i A') : '',
                    'started_by' => $schedule->startedUser->name ?? '',
                    'started_at' => $schedule->started_at ? Carbon::parse($schedule->started_at)->format('d-m-Y h:i A') : '',
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

    public function openAnnouncementModal()
    {
        $this->announceModal = true;
    }

    public function createAnnouncement()
    {
        $this->authorize('store', Announcement::class);
        $this->announcementForm->store($this->activeWarehouse->short);
        $this->alert('success', 'Announcement added');
        $this->closeAnnouncementModal();
    }

    public function closeAnnouncementModal()
    {
        $this->announceModal = false;
        $this->announcementForm->reset();
        $this->resetValidation('announcementForm.message');
    }

    public function cancelAnnouncement(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);
        $response = $this->announcementForm->delete($announcement);
        if($response) {
            $this->alert('success', 'Announcement Cancelled');
            return;
        }
        $this->alert('error', 'cancelling failed');

    }

    public function fetchDriverSkills($field, $value)
    {

        $key = explode(".", $field)[1];
        $this->filteredSchedules[$key]['driver_id'] = $value;
        $driver = user::find($value);
        $skills = $driver->skills?->skills;
        $skills = $skills ? explode(",", $skills) : null;
        $this->filteredSchedules[$key]['driver_skills'] = $skills;
    }

    public function attchQueryParam($id)
    {
        $this->selectedScheduleId = $id;
    }

    public function showCargoModal($truckScheduleId)
    {
        $this->cargoSorting = true;
        $this->reset([
            'cargoItems',
            'cargoError'
        ]);
        $truckschedule = TruckSchedule::with('orderSchedule')->find($truckScheduleId);
        if(!$truckschedule) {
            $this->cargoError = ['status' => true, 'message' => ' schedule not found'];
            return;
        }

        $this->cargoItems = $truckschedule->orderSchedule
        ->sortByDesc(function ($schedule) {
            return strtotime($schedule->expected_arrival_time);
        })
        ->map(function ($schedule) {
            return [
                'schedule_id' => $schedule->scheduleId(),
                'sx_ordernumber' => $schedule->sx_ordernumber,
                'order_number_suffix' => $schedule->order_number_suffix,
                'line_item' => $schedule->line_item,
            ];
        })
        ->toArray();

        if(empty($this->cargoItems)) {
            $this->cargoError = ['status' => true, 'message' => 'No cargo items found'];
            return;
        }
    }

    public function closeCargoModal()
    {
        $this->cargoSorting = false;
        $this->reset([
            'cargoItems',
            'cargoError'
        ]);
    }
}
