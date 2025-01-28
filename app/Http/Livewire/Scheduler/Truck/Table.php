<?php

namespace App\Http\Livewire\Scheduler\Truck;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Location;
use App\Models\Scheduler\Truck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    use AuthorizesRequests;
    public $whseId;
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');

        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function boot(): void
    {
        // $this->authorize('viewAny', Truck::class);
    }

    public function columns(): array
    {
        return [

            Column::make('Id', 'id')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('scheduler.truck.show', ['truck' => $row->id]).'" class="text-primary text-decoration-underline">' . $value . '</a>';
                })
                ->hideIf(1),

            Column::make('Name', 'truck_name')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return '<a href="'.route('scheduler.truck.show', ['truck' => $row->id]).'" class="text-primary text-decoration-underline">' . $value . '</a>';
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('VIN#', 'vin_number')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Model And Make', 'model_and_make')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->excludeFromColumnSelect(),

            Column::make('Year')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->excludeFromColumnSelect(),

            Column::make('Cubic Storage Space')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->excludeFromColumnSelect(),

            Column::make('Color', 'color')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect(),

            Column::make('Created At', 'created_at')
                ->sortable()->searchable()->deselected()
                ->format(function ($value) {
                    if ($value) {
                        return $value->format(config('app.default_datetime_format'));
                    }
                }),

        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function builder(): Builder
    {
        return Truck::query()->where('whse', $this->whseId);
    }

    public function fetchIcon($type)
    {
        if ($type == 'TRAILER') {
            return '<i class="fas fa-trailer"></i> ';
        }
        if ($type == 'TRUCK') {
            return '<i class="fas fa-truck-pickup"></i> ';
        }
        if ($type == 'INCOMPLETE VEHICLE') {
            return '<i class="fas fa-truck"></i> ';
        }

        return '<i class="fas fa-truck"></i>';
    }
}
