<?php

namespace App\Http\Livewire\Equipment\Unavailable\Report;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Equipment\UnavailableReport;
use App\Models\Equipment\UnavailableUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

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
        //$this->authorize('viewAny', NotificationTemplate::class);
    }

    public function columns(): array
    {
        return [

            Column::make('Id', 'id')
                ->hideIf(1)
                ->html(),

            Column::make('Report Date', 'report_date')
                ->sortable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('equipment.unavailable.show', $row->id).'" wire:navigate class="text-primary text-decoration-underline">Report for '.$row->report_date->toFormattedDateString().'</a>';
                })
                ->html(),

            Column::make('Status', 'status')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    if($value == 'Pending Review') return $value.' <span class="badge bg-light-warning"><i class="fas fa-exclamation-triangle"></i> Due in '.$row->report_date->addDays(7)->diffForHumans().'</span>';
                    return $value;
                })
                ->html(),
            
                


            //BooleanColumn::make('Active', 'is_active')->sortable(),
        ];
    }

    public function builder(): Builder
    {
        $query = UnavailableReport::where('user_id', auth()->user()->id);
        
        return $query;
    }

    public function filters(): array
    {
        return [
           
        ];
    }
}