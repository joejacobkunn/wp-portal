<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory\Inventory;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Warehouse;
use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class Table extends DataTableComponent
{

    public array $bulkActions = [
        'updateSelected' => 'Update Selected',
        'holdSelected' => 'Hold Selected',
        'deleteSelected' => 'Delete Selected',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('warehouse.title', 'asc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Product', 'product')
            ->secondaryHeader($this->getFilterByKey('product'))
            ->sortable()->searchable()->excludeFromColumnSelect()
            ->format(function ($value, $row)
            {
                if($row->is_on_hold)
                {
                    $badge = '<span class="badge bg-light-warning float-end"><i class="fas fa-pause-circle"></i></span>';
                }else{
                    if($row->qty > 0)
                    {
                        $badge = '<span class="badge bg-light-success float-end"><i class="fas fa-check-circle"></i></span>';
                    }
                    else
                    {
                        $badge = '<span class="badge bg-light-secondary float-end"><i class="far fa-eye-slash"></i></span>';
                    }
    
                }

                return '<a href="' . route('equipment.floor-model-inventory.show', ['floorModel' => $row->id]) . '"
                wire:click.prevent="viewDetails(' . $row->id . ')"
                class="text-primary text-decoration-underline">' .
                $value . '</a>' . $badge;

            })
            ->html(),

            Column::make('Warehouse', 'warehouse.title')
            ->secondaryHeader($this->getFilterByKey('warehouse'))
            ->excludeFromColumnSelect()
            ->sortable(function(Builder $query, string $direction) {
                return $query->orderBy('warehouse.title', $direction);
            })
            ->format(function ($value, $row) {
                return $row->warehouse->title ?? 'N/A';
            })
            ->html(),

            Column::make('Quantity', 'qty')
            ->secondaryHeader($this->getFilterByKey('quantity'))
            ->sortable()
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('On Hold', 'is_on_hold')
            ->hideIf(1),

            Column::make('Last Updated By', 'sx_operator_id')
            ->secondaryHeader($this->getFilterByKey('operator'))
            ->excludeFromColumnSelect()
            ->format(function ($value, $row)
            {
                return $row->operator?->name.' <span class="badge bg-light-secondary">'.strtoupper($value).'</span>';
            })
            ->html(),

            Column::make('Updated at', 'updated_at')
            ->sortable()
            ->excludeFromColumnSelect()
            ->format(function ($value, $row)
            {
                return  '<span title="'.$row->updated_at.'">'.$row->updated_at?->diffForHumans().'</span>';
            })
            ->html(),

            Column::make('Created at', 'created_at')
            ->sortable()
            ->excludeFromColumnSelect()
            ->format(function ($value, $row)
            {
                return  '<span title="'.$row->created_at.'">'.$row->created_at?->format(config('app.default_datetime_format')).'</span>';
            })
            ->html()
        ];
    }

    public function filters(): array
    {
        $warehouses = Warehouse::select('id','title')->get();
        foreach($warehouses as $item) {
            $data[$item->id] = $item->title;
        }
        return [

            TextFilter::make('Product')
            ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Product',
                    'maxlength' => '15',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('product', 'like', '%'.$value.'%');
                }),

            TextFilter::make('Warehouse')
            ->hiddenFromAll()
            ->config([
                'placeholder' => 'Search Warehouse',
                'maxlength' => '15',
            ])
            ->filter(function (Builder $builder, string $value) {
                $builder->whereHas('warehouse', function($query) use ($value) {
                    $query->where('title', 'like', '%'.$value.'%');
                });
            }),
            TextFilter::make('Operator')
            ->hiddenFromAll()
            ->config([
                'placeholder' => 'Search Operator',
                'maxlength' => '15',
            ])
            ->filter(function (Builder $builder, string $value) {
                $builder->whereHas('operator', function($query) use ($value) {
                    $query->where('name', 'like', '%'.$value.'%');
                });
            }),

            TextFilter::make('Quantity')
            ->hiddenFromAll()
            ->config([
                'placeholder' => 'Search Quantity',
                'maxlength' => '8',
            ])
            ->filter(function (Builder $builder, string $value) {
                $builder->where('qty', 'like', '%'.$value.'%');
            })
        ];
    }

    public function viewDetails($id)
    {
        $this->storeTableStateInSession();
        return redirect()->route('equipment.floor-model-inventory.show', $id);
    }

    public function mount()
    {
        if (session()->has('table_state')) {
            $state = session('table_state');

            foreach ($state['filters'] as $key => $value) {
                $this->setFilter($key, $value);
            }
            session()->forget('table_state');
        }
    }

    public function storeTableStateInSession()
    {
        session(['table_state' => [
            'filters' => $this->getAppliedFilters(),
        ]]);
    }

    public function deleteSelected()
    {
        $rows = $this->getSelected();
        $this->storeTableStateInSession();
        $this->dispatch('bulkDelete', $rows);
        $this->clearSelected();

    }
    public function updateSelected()
    {
        $rows = $this->getSelected();
        $this->storeTableStateInSession();
        $this->dispatch('bulkUpdate', $rows);

    }

    public function holdSelected()
    {
        $rows = $this->getSelected();
        $this->storeTableStateInSession();
        $this->dispatch('bulkHold', $rows);
    }

    public function builder(): Builder
    {
        $query = FloorModelInventory::with(['products', 'warehouse:id,cono,title'])->select('floor_model_inventory.id','product','whse','qty','sx_operator_id');
        return $query;
    }

}
