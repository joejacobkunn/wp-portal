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
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ReportTable extends DataTableComponent
{
    use LivewireAlert;

    public $warehouses;
    public $brands;
    public $lines;
    public $operators;
    public array $filterValues = [];

    public function configure(): void
    {
        $this->setPrimaryKey('serial');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
        $this->setSearchDebounce(500);
        $this->setLoadingPlaceholderEnabled();

        $this->setConfigurableAreas([
            'toolbar-right-start' => 'livewire.equipment.warranty.warranty-import.partials.settings-table-btn',
        ]);

    }
    public array $bulkActions = [
        'registerBulk' => 'Register',
        'unregisterBulk' => 'Unregister',
        'ignore' => 'Ignore'
    ];

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
                        return (string)$value.' <span class="badge bg-light-danger float-end"><i class="fas fa-exclamation-triangle"></i></span>';
                    }

                    return (string)$value.' <span class="badge bg-light-success float-end"><i class="far fa-check-circle"></i></span>';
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

                Column::make('Customer Type', 'cust_type')
                ->hideIf(1)
                ->html(),


            Column::make('Description', 'description')
                ->hideIf(1)
                ->html(),

            Column::make('Address', 'address')
                ->hideIf(1)
                ->html(),

            Column::make('Address2', 'address2')
                ->hideIf(1)
                ->html(),

                Column::make('City', 'city')
                ->hideIf(1)
                ->html(),

                Column::make('State', 'state')
                ->hideIf(1)
                ->html(),

                Column::make('Zip', 'zip')
                ->hideIf(1)
                ->html(),

            Column::make('Customer Name', 'customer_name')
                ->format(function ($value, $row) {
                    return $value.' ('.$row->cust_no.')';
                })
                ->sortable()
                ->searchable(),

            Column::make('Ship To', 'shiptoname')
                ->format(function ($value, $row) {
                    $address2 = (!empty($row->address2)) ? $row->address2.', ' : '';
                    return $value.', '.$row->address.', '.$address2.$row->city.', '.$row->state.', '.$row->zip;
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
                    if($row->registration_date == '01/01/01' || $row->registration_date == '2001-01-01')
                    {
                        return '<span class="badge bg-light-secondary">Ignored</span> <div class="btn-group" role="group" aria-label="Basic example" data-cust-no="\''.$row->cust_no.'\'"  data-serial="\''.$row->serial.'\'">
                        <button type="button" class="btn btn-sm btn-outline-warning warrantyUnregister">Reset</button></div>';
                    }

                    if(empty($row->registration_date))
                    {
                        return '<div class="btn-group " role="group" aria-label="Basic example" data-cust-no="'.$row->cust_no.'"  data-serial="'.$row->serial.'">
                            <button type="button" class="btn btn-sm btn-outline-primary warrantyRegister">Register</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary ignoreRegistration">Ignore</button></div>';
                    }

                    return '<span class="date-span">'.$value.'</span><div class="btn-group " role="group" aria-label="Basic example" data-cust-no="'.$row->cust_no.'"  data-serial="'.$row->serial.'">
                        <button type="button" class="btn btn-sm btn-outline-danger warrantyUnregister">Unregister</button></div>';

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
                ->options([''=>'All', 'registered'=>'Registered','non-registered' => 'Non Registered', 'ignored' => 'Ignored'])
                ->filter(function(Builder $builder, string $value) {
                    if($value == 'registered')
                        $builder->whereNot('registration_date', '');
                    if($value == 'non-registered')
                        $builder->where('registration_date', null);
                    if($value == 'ignored')
                        $builder->whereIn('registration_date', ['01/01/01','01-01-2001']);
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

    public function registerBulk()
    {
        $rows = $this->getSelected();
        foreach($rows as $row)
        {
            $report = Report::where('serial', $row)->first();
            $report->update(['registration_date' => date("m/d/y"), 'registered_by' => auth()->user()->sx_operator_id]);
            DB::connection('sx')->statement("UPDATE pub.icses SET user9 = '".date("m/d/y")."' , user4 = '".auth()->user()->sx_operator_id."' where cono = 10 and currstatus = 's' and LTRIM(RTRIM(UPPER(icses.serialno))) = '".$this->clean($report->serial)."' and custno = '".$this->clean($report->cust_no)."'");

        }
        $this->clearSelected();
        $this->alert('success', count($rows).' products registered!');

    }

    public function unregisterBulk()
    {
        $rows = $this->getSelected();
        foreach($rows as $row)
        {
            $report = Report::where('serial', $row)->first();
            $report->update(['registration_date' => '', 'registered_by' => '']);
            DB::connection('sx')->statement("UPDATE pub.icses SET user9 = NULL , user4 = '' where cono = 10 and currstatus = 's' and LTRIM(RTRIM(UPPER(icses.serialno))) = '".$this->clean($report->serial)."' and custno = '".$this->clean($report->cust_no)."'");
        }
        $this->clearSelected();
        $this->alert('success', count($rows).' products unregistered!');

    }

    public function ignoreBulk()
    {
        $rows = $this->getSelected();
        foreach($rows as $row)
        {
            $report = Report::where('serial', $row)->first();
            $report->update(['registration_date' => '2001-01-01', 'registered_by' => auth()->user()->sx_operator_id]);
            DB::connection('sx')->statement("UPDATE pub.icses SET user9 = '2001-01-01' , user4 = '".auth()->user()->sx_operator_id."' where cono = 10 and currstatus = 's' and LTRIM(RTRIM(UPPER(icses.serialno))) = '".$this->clean($report->serial)."' and custno = '".$this->clean($report->cust_no)."'");
        }
        $this->clearSelected();
        $this->alert('success', count($rows).' products ignored!');

    }

    public function export()
    {
        $this->dispatch('exportWarrantyRecords');
    }

    private function clean($string) {
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
     }



}
