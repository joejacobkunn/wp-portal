<?php

namespace App\Http\Livewire\Scheduler\Schedule;


use App\Models\Scheduler\Zones;
use App\Models\Scheduler\Schedule;
use App\Enums\Scheduler\ScheduleEnum;
use App\Enums\Scheduler\ScheduleStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Http\Livewire\Component\DataTableComponent;
use App\Http\Livewire\Scheduler\Schedule\Traits\ScheduleData;
use App\Models\Core\User;
use App\Models\Scheduler\Truck;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class Table extends DataTableComponent
{
    use ScheduleData;

    public $activeTab;

    public $whse;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

        if ($this->activeTab == 'unconfirmed') {
            $this->setDefaultSort('schedule_date', 'asc');
        }
    }


    public function columns(): array
    {
        $columns = [
            Column::make('Id', 'id')
                ->hideIf(1),
            Column::make('Schedule Priority', 'travel_prio_number')
                ->hideIf(1),

            Column::make('SX Order No', 'sx_ordernumber')
            ->excludeFromColumnSelect()
            ->searchable()
            ->format(function ($value, $row) {
                return '<a href="#"
                    wire:click.prevent="$dispatch(\'schedule-event-modal-open\', { id: ' . $row->id . ' })"
                    class="text-primary text-decoration-underline">'
                    . $value . '-' . $row->order_number_suffix .
                '</a>';
            })
            ->html(),

            Column::make('Type', 'type')
                ->sortable()
                ->secondaryHeader($this->getFilterByKey('type'))
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    $typeEnum = ScheduleEnum::tryFrom($value);
                    return $typeEnum ? $typeEnum->label() : $value;
                }),

            Column::make('Schedule Date', 'schedule_date')
                ->excludeFromColumnSelect()
                ->hideIf($this->activeTab == 'today' || $this->activeTab == 'tomorrow')
                ->format(function ($value, $row) {
                    if ($value) {
                        $dateObj = Carbon::parse($value);
                        $dateStr = $dateObj->format(config('app.default_date_format'));

                        if ($this->activeTab == 'unconfirmed') {
                            $dateStr .= ' (' . $dateObj->diffForHumans(). ')';
                        }

                        return $dateStr;
                    }
                }),

            Column::make('Order No Suffix', 'order_number_suffix')
                ->hideIf(1),

            Column::make('Customer Name', 'sx_ordernumber')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->order?->customer?->name.' (#'.$row->order?->customer?->sx_customer_number.')';
                }),


            Column::make('Truck Name', 'truck_schedule_id')
                ->secondaryHeader($this->getFilterByKey('truck'))
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->truckSchedule?->truck?->truck_name;
                }),

            Column::make('Zone', 'truck_schedule_id')
                ->secondaryHeader($this->getFilterByKey('zone'))
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->truckSchedule?->zone?->name;
                }),

                Column::make('Time Slot', 'truck_schedule_id')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->truckSchedule?->start_time.' - '.$row->truckSchedule?->end_time;
                }),

                Column::make('Created By', 'created_by')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->user?->name;
                }),

                Column::make('Driver', 'truckSchedule.driver_id')
                ->secondaryHeader($this->getFilterByKey('drivers'))
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->truckSchedule?->driver?->name;
                }),

            ];

        if ($this->activeTab == 'today') {
            $columns[] = Column::make('ETA', 'expected_arrival_time')
                ->format(function ($value, $row) {
                    return (empty($value)) ? 'n/a' : Carbon::parse($value)->format('h:i A');
                });
            $columns[] = Column::make('Latest Comment', 'id')
                ->format(function ($value, $row) {
                    return strip_tags($row->latestComment?->comment);
                });
        }

        $columns[] = Column::make('Status', 'status')
        ->excludeFromColumnSelect()
        ->html()
        ->format(function ($value, $row)
        {
            return '<span class="badge bg-'.$row->status_color_class.'">'. ScheduleStatusEnum::tryFrom($value)->label() .'</span>';
            return $row->truckSchedule?->zone?->name;
        });


        return $columns;
    }

    public function getScheduleTypesProperty()
    {
        return ScheduleEnum::getArray();
    }

    public function getTrucksProperty()
    {
        return Truck::select('id', 'truck_name', 'whse')->limit(100)->get();
    }

    public function getDriversProperty()
    {
        return User::whereIn('title', ['Driver', 'Service Technician'])->select('id', 'name')->limit(100)->get();
    }

    public function getZonesProperty()
    {
        return Zones::select('id', 'name')->orderBy('name')->limit(100)->pluck('name', 'id')->toArray();
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Type', 'type')
                ->options(
                    ['' => 'All Types'] +
                   $this->schedule_types
                )
                ->hiddenFromMenus()
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('schedules.type', $value);
                }),

            SelectFilter::make('Truck', 'truck')
                ->options(
                    ['' => 'All Trucks'] +
                    $this->trucks->pluck('truck_name', 'id')->toArray()
                )
                ->hiddenFromMenus()
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('truck_schedules.truck_id', $value);
                }),

            SelectFilter::make('Zone', 'zone')
                ->options(
                    [''=> 'All Zones'] +
                    $this->zones
                )
                ->hiddenFromMenus()
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('truck_schedules.zone_id', $value);
                }),

            SelectFilter::make('Drivers', 'drivers')
                ->options(
                    [''=> 'All Drivers'] +
                    $this->drivers->pluck('name', 'id')->toArray()
                )
                ->hiddenFromMenus()
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('truck_schedules.driver_id', $value);
                }),

            DateFilter::make('Scheduled From', 'scheduled_from')
                ->config([
                    'format' => config('app.default_date_format')
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->whereDate('schedules.schedule_date', '>=', $value);
                    }
                }),

            DateFilter::make('Scheduled To', 'scheduled_to')
                ->config([
                    'format' => config('app.default_date_format')
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->whereDate('schedules.schedule_date', '>=', $value);
                    }
                }),
        ];
    }

    public function builder(): Builder
    {
        if ($this->activeTab == 'today') {
            $scheduleQuery = $this->queryByDate(Carbon::now()->toDateString())->with('latestComment');
        } elseif ($this->activeTab == 'tomorrow') {
            $scheduleQuery = $this->queryByDate(Carbon::now()->addDay()->toDateString());
        }  elseif ($this->activeTab == 'unconfirmed') {
            $scheduleQuery = $this->queryByStatus('unconfirmed');
        } else {
            $scheduleQuery = $this->scheduleBaseQuery();
        }

        $scheduleQuery->with([
            'truckSchedule' => function($query) {
                $query->whereNull('deleted_at')
                    ->with('zone:id,name')
                    ->with('truck:id,truck_name')
                    ->with('driver:id,name')
                    ->select('id', 'start_time', 'end_time', 'truck_id', 'zone_id', 'driver_id');
            },
            'order' => function($query) {
                $query->whereNull('deleted_at')
                    ->select('id', 'order_number', 'sx_customer_number')
                    ->with(['customer' => function($query) {
                        $query->whereNull('deleted_at')
                            ->select('id', 'name', 'email', 'phone', 'sx_customer_number');
                    }]);
            }
        ]);

        if (!empty($this->whse)) {
            //@TODO update after schedule whse update
            $scheduleQuery->whereIn('truck_schedules.truck_id', $this->trucks->where('whse', $this->whse)->pluck('id')->toArray());

            //$scheduleQuery->where('orders.whse', $this->whse);
        }

        return $scheduleQuery;
    }
}
