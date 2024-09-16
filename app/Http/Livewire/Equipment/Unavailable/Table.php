<?php

namespace App\Http\Livewire\Equipment\Unavailable;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Warehouse;
use App\Models\Equipment\UnavailableUnit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
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
        $this->setDefaultSort('unavailable_equipments.created_at', 'desc');

        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function mount()
    {
        $this->setFilter('user', 'show_mine');
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
                    if(empty($value)) return '<span class="badge bg-light-warning">Not Set</span>';
                    return $value;
                })
                ->html(),

                Column::make('Warehouse', 'whse')
                ->secondaryHeader($this->getFilterByKey('whse'))
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->html(),

                Column::make('Possessed By', 'possessed_by')
                ->secondaryHeader($this->getFilterByKey('possessed_by'))
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    $name = $row->user ? $row->user->name.' '. $row->user?->abbreviation.'('.$value.')' : $value;
                    return strtoupper($name);
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

                Column::make('Hours', 'hours')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
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
        $query = UnavailableUnit::where('cono', auth()->user()->account->sx_company_number)->where('is_unavailable', 1);

        return $query;
    }

    public function filters(): array
    {
        $warehouses = Warehouse::where('cono',40)->pluck('short')->toArray();
            foreach ($warehouses as $item) {
                $whse[$item] = strtoupper($item);
            }
        return [
            TextFilter::make('Possessed By', 'possessed_by')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search User',
                    'maxlength' => '15',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('is_unavailable', 1)->whereHas('user', function ($query) use ($value) {
                        $query->where('name', 'like', '%' . $value . '%')
                            ->orWhere('abbreviation', 'like', '%' . $value . '%');
                    })
                    ->orWhere('possessed_by', 'like', '%' . $value . '%');
                }),

                SelectFilter::make('Warehouse', 'whse')
                ->options(['' => 'All'] + $whse)
                ->hiddenFromAll()
                    ->filter(function (Builder $builder, string $value) {
                        $builder->where('whse', $value);
                }),

                SelectFilter::make('Equipment Visibility', 'user')
                ->options([
                    'show_mine' => 'Show Mine',
                    '' => 'Show All',
                    ])
                    ->filter(function (Builder $builder, string $value) {
                        $builder->where('is_unavailable', 1)->where('possessed_by', $value=='show_mine'? Auth::user()->unavailable_equipments_id : $value );
                })
        ];
    }

    public function setFilterValue($filter, $value)
    {
        $this->setFilterDefaults();
        $this->setFilter($filter, $value);
    }
}
