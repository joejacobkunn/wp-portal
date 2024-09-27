<?php

namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Warehouse;
use App\Models\Equipment\Warranty\Report;
use App\Models\Product\Brand;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class ReportTable extends DataTableComponent
{
    public $warehouses;
    public $brands;
    public array $filterValues = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

    }

    public function mount()
    {
        $this->warehouses = Warehouse::orderBy('title')->pluck('title', 'short')->toArray();
        $this->brands = Brand::orderBy('name')->pluck('name', 'name')->toArray();
    }

    public function columns(): array
    {
        return [
            Column::make('Store', 'store')
                ->secondaryHeader($this->getFilterByKey('store'))
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->sortable()
                ->searchable(),

            Column::make('Customer Number', 'cust_no')
                ->hideIf(1)
                ->html(),

            Column::make('Description', 'description')
                ->hideIf(1)
                ->html(),

            Column::make('Customer Name', 'customer_name')
                ->format(function ($value, $row) {
                    return $value.' ('.$row->cust_no.')';
                })
                ->sortable()
                ->searchable(),
                Column::make('Order Number', 'order_no')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Brand', 'brand')
                ->secondaryHeader($this->getFilterByKey('brand'))
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Prod Line', 'prodline')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Model', 'model')
                ->searchable()
                ->format(function ($value, $row) {
                    return $value.' ('.$row->description.')';
                })
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Serial', 'serial')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Sold On', 'sold')
                ->format(function ($value, $row) {
                    return date("F j, Y", strtotime($value));
                })
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Reg Date', 'registration_date')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Reg By', 'registered_by')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('store')
                ->options([''=>'All Stores']+$this->warehouses)
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereRaw('LOWER(store) = ?', [strtolower($value)]);
                }),

            // Add more filters here as needed
            SelectFilter::make('brand')
                ->options(['All Brands'] + $this->brands)
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('brand', $value);
                }),

            DateRangeFilter::make('Sold On', 'sold_on')
            ->hiddenFromMenus()
                ->config(['placeholder' => 'Enter Date Range'])
                ->filter(function (Builder $builder, array $dateRange) { // Expects an array.
                    $builder
                        ->whereDate('sold', '>=', $dateRange['minDate']) // minDate is the start date selected
                        ->whereDate('sold', '<=', $dateRange['maxDate']); // maxDate is the end date selected
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = Report::query();

        // Apply filters
        foreach ($this->getFilters() as $filter) {
            $filterName = $filter->getName();
            if (isset($this->filterValues[$filterName]) && $this->filterValues[$filterName] !== '') {
                $query->where($filterName, $this->filterValues[$filterName]);
            }
        }
        return $query;
    }

    public function updatedFilterValues($value, $key)
    {
        $this->resetPage();
    }
}
