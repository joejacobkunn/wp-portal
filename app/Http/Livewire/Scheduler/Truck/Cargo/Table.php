<?php

namespace App\Http\Livewire\Scheduler\Truck\Cargo;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Scheduler\CargoConfigurator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    use AuthorizesRequests;
    public $whseShort;
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('truck_cargo_configurator.created_at', 'desc');

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
                ->hideIf(1),

            Column::make('Product Category', 'productCategory.name')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return '<a href="'.route('scheduler.truck.cargo.show', ['cargoConfigurator' => $row->id]).'" wire:navigate class="text-primary text-decoration-underline">' . $value . '</a>';
                })
                ->excludeFromColumnSelect()
                ->html(),
            Column::make('Height', 'height')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value.' ft';
                })
                ->excludeFromColumnSelect()
                ->html(),
            Column::make('Width', 'width')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value.' ft';
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Length', 'length')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value.' ft';
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Weight', 'weight')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value.' lb';
                })
                ->excludeFromColumnSelect()
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        return CargoConfigurator::query()->where('whse', $this->whseShort);
    }
}
