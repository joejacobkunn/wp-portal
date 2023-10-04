<?php

namespace App\Http\Livewire\Reporting;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    use AuthorizesRequests;


    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function boot(): void
    {

    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('reporting.show', $row->id).'" class="text-decoration-underline">'.$value.'</a>';
                })
                ->html(),

            Column::make('Report Name', 'name')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('reporting.show', $row->id).'" class="text-decoration-underline">'.$value.'</a>';
                })
                ->html(),

            Column::make('Description', 'description')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect(),
        ];
    }

    public function filters(): array
    {
        return [
            
        ];
    }

    public function builder(): Builder
    {
        return Report::query();
    }
}
