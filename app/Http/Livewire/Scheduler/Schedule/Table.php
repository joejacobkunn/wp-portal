<?php

namespace App\Http\Livewire\Scheduler\Schedule;


use App\Models\Scheduler\Zones;
use App\Models\Scheduler\Schedule;
use App\Enums\Scheduler\ScheduleEnum;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Scheduler\Truck;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class Table extends DataTableComponent
{
    public $activeTab;

    public $whse;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hideIf(1),

            Column::make('SX Order No', 'sx_ordernumber')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),

            Column::make('type', 'type')
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
                    return Carbon::parse($value)->toDateString();
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
        $scheduleQuery = Schedule::with([
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
         ])
         ->leftJoin('truck_schedules', 'truck_schedules.id', '=', 'schedules.truck_schedule_id')
         ->whereNull('truck_schedules.deleted_at')
         ->leftJoin('orders', 'orders.order_number', '=', 'schedules.sx_ordernumber')
         ->whereNull('orders.deleted_at')
         ->leftJoin('customers', 'orders.sx_customer_number', '=', 'customers.sx_customer_number')
         ->whereNull('customers.deleted_at');

        if (!empty($this->activeTab)) {
            if ($this->activeTab == 'today') {
                $scheduleQuery->whereDate('schedules.schedule_date', Carbon::now()->toDateString());
            } elseif ($this->activeTab == 'tomorrow') {
                $scheduleQuery->whereDate('schedules.schedule_date', Carbon::now()->addDay()->toDateString());
            }  elseif ($this->activeTab == 'confirmed') {
                $scheduleQuery->where('schedules.status', 'confirmed');
            }
        }

        if (!empty($this->whse)) {
            $scheduleQuery->whereIn('truck_schedules.truck_id', $this->trucks->where('whse', $this->whse)->pluck('id')->toArray());
        }

        return $scheduleQuery;
    }
}
