<?php

namespace App\Http\Livewire\ReportingDashboard;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Report\Dashboard;
use App\Models\Report\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

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
                    return '<a href="'.route('reporting-dashboard.show', $row->id).'" class="text-decoration-underline">'.$value.'</a>';
                })
                ->html(),

            Column::make('Dashboard Name', 'name')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('reporting-dashboard.show', $row->id).'" class="text-decoration-underline">'.$value.'</a>';
                })
                ->html(),

            Column::make('Reports', 'reports')
                ->format(function ($value, $row) {
                    return Report::whereIn('id', $value)->implode('name', ', ');
                })
                ->excludeFromColumnSelect(),

            Column::make('URL', 'reports')
                ->format(function ($value, $row) {
                    return '<a target="_blank" href="'.route('reporting-dashboard.broadcast', $row->id).'" class="text-decoration-underline">Go to Public Page</a>';
                })->html()
                ->excludeFromColumnSelect(),

            BooleanColumn::make('Active', 'is_active')->excludeFromColumnSelect(),

        ];
    }

    public function filters(): array
    {
        return [
            
        ];
    }

    public function builder(): Builder
    {
        return Dashboard::where('account_id', auth()->user()->account->id);
    }
}
