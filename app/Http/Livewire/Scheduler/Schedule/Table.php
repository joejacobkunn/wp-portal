<?php

namespace App\Http\Livewire\Scheduler\Schedule;


use App\Models\Scheduler\Zones;
use App\Models\Scheduler\Schedule;
use App\Enums\Scheduler\ScheduleEnum;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Http\Livewire\Component\DataTableComponent;
use App\Http\Livewire\Scheduler\Schedule\Traits\ScheduleData;
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

            Column::make('SX Order No', 'sx_ordernumber')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),

            Column::make('Type', 'type')
                ->sortable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    $typeEnum = ScheduleEnum::tryFrom($value);
                    return $typeEnum ? $typeEnum->label() : $value;
                }),

            Column::make('Schedule Date', 'schedule_date')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    if ($value) {
                        $dateObj = Carbon::parse($value);
                        $dateStr = $dateObj->toDateString();

                        if ($this->activeTab == 'unconfirmed') {
                            $dateStr .= ' (' . $dateObj->diffForHumans(). ')';
                        }

                        return $dateStr;
                    }
                }),

            Column::make('Order No Suffix', 'order_number_suffix')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),

            Column::make('Customer Name', 'sx_ordernumber')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->order?->customer?->name;
                }),

            Column::make('SX Customer No', 'sx_ordernumber')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->order?->customer?->sx_customer_number;
                }),

            Column::make('Truck Name', 'truck_schedule_id')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->truckSchedule?->truck?->truck_name;
                }),

            Column::make('Zone', 'truck_schedule_id')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return $row->truckSchedule?->zone?->name;
                }),
            ];

        if ($this->activeTab == 'today') {
            $columns[] = Column::make('Latest Comment', 'id')
                ->format(function ($value, $row) {
                    return strip_tags($row->latestComment?->comment);
                });
        }

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

    public function getZonesProperty()
    {
        return Zones::select('id', 'name')->limit(100)->pluck('name', 'id')->toArray();
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Type', 'type')
                ->options(
                   $this->schedule_types
                )
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('schedules.type', $value);
                }),

            SelectFilter::make('Truck', 'truck')
                ->options(
                    $this->trucks->pluck('truck_name', 'id')->toArray()
                )
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('truck_schedules.truck_id', $value);
                }),

            SelectFilter::make('Zone', 'zone')
                ->options(
                    $this->zones
                )
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('truck_schedules.zone_id', $value);
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
                    ->select('id', 'start_time', 'end_time', 'truck_id', 'zone_id');
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
            $scheduleQuery->where('orders.whse', $this->whse);
        }

        return $scheduleQuery;
    }
}
