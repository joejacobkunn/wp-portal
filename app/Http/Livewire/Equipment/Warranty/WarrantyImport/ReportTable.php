<?php

namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Operator;
use App\Models\Core\Warehouse;
use App\Models\Equipment\Warranty\Report;
use App\Models\Product\Brand;
use App\Models\Product\Line;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;

class ReportTable extends DataTableComponent
{
    public $warehouses;
    public $brands;
    public $lines;
    public $operators;
    public array $filterValues = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
        $this->setSearchDebounce(500);
        $this->setLoadingPlaceholderEnabled();


    }

    public function mount()
    {
        $this->warehouses = Warehouse::orderBy('title')->pluck('title', 'short')->toArray();
        $this->brands = Brand::orderBy('name')->pluck('name', 'name')->toArray();
        $this->lines = Line::orderBy('name')->pluck('name', 'name')->toArray();
        $this->operators = Operator::orderBy('name')->pluck('name', 'name')->toArray();
        $this->setFilter('status', '');
    }

    public function columns(): array
    {
        return [

            Column::make('Serial', 'serial')
                ->searchable()
                ->format(function ($value, $row) {
                    if(empty($row->registration_date))
                    {
                        return $value.' <span class="badge bg-light-danger float-end"><i class="fas fa-exclamation-triangle"></i></span>';
                    }

                    return $value.' <span class="badge bg-light-success float-end"><i class="far fa-check-circle"></i></span>';
                })
                ->excludeFromColumnSelect()
                ->html(),


            Column::make('Model', 'model')
                ->searchable()
                ->format(function ($value, $row) {
                    return $value.' ('.$row->description.')';
                })
                ->excludeFromColumnSelect()
                ->html(),


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
                ->secondaryHeader($this->getFilterByKey('lines'))
                ->excludeFromColumnSelect()
                ->html(),


                Column::make('Sold On', 'sold')
                ->searchable()
                ->sortable()
                ->secondaryHeader($this->getFilterByKey('sold_on'))
                ->format(function ($value, $row) {
                    return date("F j, Y", strtotime($value));
                })
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Reg Date', 'registration_date')
                ->secondaryHeader($this->getFilterByKey('reg_date'))
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    if(empty($row->registration_date))
                    {
                        return $value.' <button wire:click="register(\''.$row->serial.'\', \''.$row->cust_no.'\')" class="btn btn-sm btn-outline-primary align-middle">Register</button>';
                    }

                    return $value;

                })

                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Reg By', 'registered_by')
                ->secondaryHeader($this->getFilterByKey('registered_by'))
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('store')
                ->hiddenFromMenus()
                ->options([''=>'All Stores']+$this->warehouses)
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereRaw('LOWER(store) = ?', [strtolower($value)]);
                }),

            SelectFilter::make('brand')
                ->options(['All Brands'] + $this->brands)
                ->hiddenFromMenus()
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereRaw('LOWER(brand) = ?', [strtolower($value)]);
                }),

            SelectFilter::make('lines')
                ->options(['All'] + $this->lines)
                ->hiddenFromMenus()
                ->filter(function(Builder $builder, string $value) {
                    $builder->whereRaw('LOWER(prodline) = ?', [strtolower($value)]);
                }),

            SelectFilter::make('Status', 'status')
                ->options([''=>'All', 'registered'=>'Registered','non-registered' => 'Non Registered'])
                ->filter(function(Builder $builder, string $value) {
                    if($value == 'registered')
                        $builder->whereNot('registration_date', '');
                    if($value == 'non-registered')
                        $builder->where('registration_date', null);
                }),

            SelectFilter::make('registered_by')
                ->options(['All'] + $this->operators)
                ->hiddenFromMenus()
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('registered_by', $value);
                }),

            DateRangeFilter::make('Sold On', 'sold_on')
            ->hiddenFromMenus()
            ->config(['placeholder' => 'Enter Date Range'])
                ->filter(function (Builder $builder, array $dateRange) {
                    $builder
                        ->whereDate('sold', '>=', $dateRange['minDate'])
                        ->whereDate('sold', '<=', $dateRange['maxDate']);
                }),

            DateRangeFilter::make('Reg Date', 'reg_date')
            ->hiddenFromMenus()
            ->config(['placeholder' => 'Enter Date Range'])
                ->filter(function (Builder $builder, array $dateRange) {
                    $builder
                        ->whereDate('registration_date', '>=', $dateRange['minDate'])
                        ->whereDate('registration_date', '<=', $dateRange['maxDate']);
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = Report::query();
         return $query;
    }

    public function register($serial_no, $cust_no)
    {
        DB::connection('sx')->statement("UPDATE pub.icses SET user9 = '".date("m/d/y")."' , user4 = '".auth()->user()->sx_operator_id."' where cono = 10 and currstatus = 's' and LTRIM(RTRIM(UPPER(icses.serialno))) = '".$serial_no."' and custno = '".$cust_no."'");
        $record = Report::where('serial',$serial_no)->where('cust_no',$cust_no)->first();
        $record->update(['registration_date' => date("m/d/y"), 'registered_by' => auth()->user()->sx_operator_id]);
    }

}
