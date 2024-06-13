<?php

namespace App\Http\Livewire\Equipment\Unavailable;

use App\Http\Livewire\Component\DataTableComponent;
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

            Column::make('Product Code', 'product_code')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('equipment.unavailable.show', $row->id).'" wire:navigate class="text-primary text-decoration-underline">'.$row->product_name.' ('.$value.')</a>';
                })
                ->html(),

            Column::make('Product Name', 'product_name')
                ->searchable()
                ->hideIf(1),

            Column::make('Serial No', 'serial_number')
                ->excludeFromColumnSelect()
                ->searchable()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->html(),
            
                Column::make('Warehouse', 'whse')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->html(),

                Column::make('Possessed By', 'possessed_by')
                ->secondaryHeader($this->getFilterByKey('possessed_by'))
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->hideIf(!auth()->user()->can('equipment.unavailable.viewall'))
                ->html(),

                Column::make('Current Location', 'current_location')
                ->excludeFromColumnSelect()
                ->searchable()
                ->format(function ($value, $row) {
                    if(empty($value)) return '<span class="badge bg-light-warning">Not Set</span>';
                    return $value;
                })
                ->html(),



            Column::make('Base Price', 'base_price')
                ->excludeFromColumnSelect()
                ->sortable()
                ->format(function ($value, $row) {
                    return '$'.number_format($value,2);
                })
                ->footer(function($rows) {
                    return '<span class="badge bg-light-success">Subtotal : ' . '$'.number_format($rows->sum('base_price'),2).'</span>';
                })
                ->secondaryHeader(function($rows) {
                    return '<span class="badge bg-light-success">Subtotal : ' . '$'.number_format($rows->sum('base_price'),2).'</span>';
                })

                ->html(),


            //BooleanColumn::make('Active', 'is_active')->sortable(),
        ];
    }

    public function builder(): Builder
    {
        $query = UnavailableUnit::where('cono', auth()->user()->account->sx_company_number);

        if(!auth()->user()->can('equipment.unavailable.viewall'))
        {
            $query->where('possessed_by', strtolower(auth()->user()->unavailable_equipments_id));
        }
        
        return $query;
    }

    public function filters(): array
    {
        return [
            TextFilter::make('Possessed By', 'possessed_by')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search By Initial',
                    'maxlength' => '3',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('possessed_by', 'like', '%'.$value.'%');
                }),
        ];
    }
}
