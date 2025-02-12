<?php

namespace App\Http\Livewire\Vehicle\Vehicle;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Account;
use App\Models\Core\Location;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

    public Account $account;

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
        $this->authorize('viewAny', Location::class);
    }

    public function columns(): array
    {
        return [

            Column::make('Id', 'id')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('vehicle.show', $row->id).'" class="text-primary text-decoration-underline">'.$value.'</a>';
                })
                ->hideIf(1)
                ->html(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return '<a href="'.route('vehicle.show', ['vehicle' => $row->id, $row]).'" class="text-primary text-decoration-underline">'.$value.'</a>';
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Year')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->hideIf(1)
                ->excludeFromColumnSelect(),

            Column::make('Make')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->hideIf(1)
                ->excludeFromColumnSelect(),

            Column::make('Model')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $this->fetchIcon($row->type).$row->year.' '.$row->make.' '.$row->model;
                })
                ->html()
                ->excludeFromColumnSelect(),

            Column::make('VIN#', 'vin')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Type', 'type')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->hideIf(1)
                ->format(function ($value, $row) {
                    return '<i class="fas fa-truck-pickup"></i> '.ucfirst(strtolower($value));
                })
                ->html(),

            Column::make('License Plate', 'license_plate_number')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->html(),

            Column::make('Created At', 'created_at')
                ->sortable()->searchable()->deselected()
                ->format(function ($value) {
                    if ($value) {
                        return $value->format(config('app.default_datetime_format'));
                    }
                }),

            Column::make('Active', 'retired_at')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return ! $value ? '<span style="color:green"><i class="far fa-check-circle"></i></span>' : '<span style="color:red"><i class="far fa-times-circle"></i></span>';
                })
                ->html(),

        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function builder(): Builder
    {
        return Vehicle::where('account_id', $this->account->id);
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
