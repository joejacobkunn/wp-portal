<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProductTable extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('products.created_at');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }
    public function columns(): array
    {
        return [
            Column::make('Product', 'prod')
            ->sortable()->searchable()->excludeFromColumnSelect()
            ->html(),
            Column::make('Brand', 'brand.name')
            ->excludeFromColumnSelect()
            ->html(),
            Column::make('Description', 'description')
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Action', 'id')
            ->format(function ($value, $row)
            {
                return '<button class="btn btn-primary" wire:click="addProduct(`'.$row->prod.'`)">Select this product </button>';
            })
            ->html(),
        ];
    }

    public function builder(): Builder
    {
        $query = Product::with(['brand', 'line'])
        ->whereNotIn('products.active', ['inactive', 'labor'])
        ->whereHas('line', function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%-E')
                  ->orWhere('name', 'like', '%-A');
            });
        });
        return $query;
    }
    public function addProduct($item)
    {
       $this->dispatch('addProductsToForm', $item);
    }

}
