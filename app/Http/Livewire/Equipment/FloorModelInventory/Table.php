<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Warehouse;
use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class Table extends DataTableComponent
{

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('floor_model_inventory.created_at');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setFilterLayout('slide-down');
       // $this->setFilterSlideDownDefaultStatusEnabled();
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Product', 'product')
            ->sortable()->searchable()->excludeFromColumnSelect()
            ->format(function ($value, $row)
            {
                return '<a  href="'.route('equipment.floor-model-inventory.show', ['floorModel'=> $row->id]).
                    '" wire:navigate class="text-primary text-decoration-underline">'.
                    $value.'</a>';
            })
            ->html(),
            Column::make('Warehouse', 'whse')
            ->excludeFromColumnSelect()
            ->sortable()
            ->format(function ($value, $row)
            {
                if ($row->whse && isset($row->warehouse->title)) {
                    return $row->warehouse->title;
                }
                return 'N/A';
            })
            ->html(),
            Column::make('Quantity', 'qty')
            ->sortable()
            ->excludeFromColumnSelect()
            ->html(),
            Column::make('SX Opertor ID', 'sx_operator_id')
            ->excludeFromColumnSelect()
            ->html(),
            Column::make('cono', 'whse')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row)
            {
                if ($row->whse && isset($row->warehouse->cono)) {
                    return $row->warehouse->cono;
                }
                return 'N/A';
            })
            ->html(),

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
                ->config([
                    'placeholder' => 'Search Product',
                    'maxlength' => '15',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('product', 'like', '%'.$value.'%');
                }),

            SelectFilter::make('Warehouse')
                ->options($data)->filter(function (Builder $builder, string $value) {
                        $builder->where('whse', $value);
                }),
            SelectFilter::make('Quantity')
                ->options(['0','1','2','3'])->filter(function (Builder $builder, string $value) {
                        $builder->where('qty', $value);
                }),
        ];
    }
    public function builder(): Builder
    {
        $query = FloorModelInventory::with(['products', 'warehouse:id,cono,title'])->select('id','product','whse','qty','sx_operator_id');
        return $query;
    }
}
