<?php

namespace App\Http\Livewire\SalesRepOverride;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\SalesRepOverride\SalesRepOverride;
use Illuminate\Database\Eloquent\Builder;

use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('sales_rep_overrides.created_at', 'desc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
            ->excludeFromColumnSelect()
            ->searchable()
            ->sortable()
            ->format(function ($value, $row) {
                return '<a  href="'.route('sales-rep-override.show', $row->id).
                '" wire:navigate class="text-primary text-decoration-underline">'.
                $value.'</a>';})
            ->html(),

            Column::make('Customer Number', 'customer_number')
            ->excludeFromColumnSelect()
            ->searchable()
            ->sortable()
            ->html(),

            Column::make('Ship To', 'ship_to')
            ->excludeFromColumnSelect()
            ->searchable()
            ->sortable()
            ->html(),

            Column::make('Prod Line', 'prod_line')
            ->excludeFromColumnSelect()
            ->searchable()
            ->sortable()
            ->html(),

            Column::make('Sales Rep', 'sales_rep')
            ->excludeFromColumnSelect()
            ->searchable()
            ->sortable()
            ->html(),

            Column::make('Last Updated By', 'user.name')
            ->excludeFromColumnSelect()
            ->searchable()
            ->sortable()
            ->html(),
        ];
    }
    public function builder(): Builder
    {
        $query = SalesRepOverride::query()
        ->select([
            'sales_rep_overrides.id',
            'customer_number',
            'ship_to',
            'prod_line',
            'sales_rep',
        ]);
        return $query;
    }
}
