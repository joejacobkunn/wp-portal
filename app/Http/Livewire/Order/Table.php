<?php

namespace App\Http\Livewire\Order;

use App\Enums\Order\OrderStatus;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Operator;
use App\Models\Core\User;
use App\Models\Order\DnrBackorder;
use App\Models\Order\Order;
use App\Models\Product\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

    public $pending_review_count = 0;

    public $follow_up_count = 0;

    public $ignored_count = 0;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setPerPageAccepted([50, 75 ,100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
        $this->setSearchDebounce(500);
        $this->setLoadingPlaceholderEnabled();
        $this->setEmptyMessage('No orders found. Use global search to search on all columns and make sure no filters are applied.');

        $this->setConfigurableAreas([
            'toolbar-right-start' => [
                'livewire.order.partials.table-status-count', [
                    'pending_review_count' => $this->pending_review_count,
                    'follow_up_count' => $this->follow_up_count,
                    'ignored_count' => $this->ignored_count
                ]
            ],
          ]);

    }


    public function boot(): void
    {
        //$this->authorize('viewAny', Location::class);
    }

    public function configuring()
    {
        $this->pending_review_count = Order::where('cono', auth()->user()->account->sx_company_number)->where('status', 'Pending Review')->count();;
        $this->follow_up_count = Order::where('cono', auth()->user()->account->sx_company_number)->where('status', 'Follow Up')->count();
        $this->ignored_count = Order::where('cono', auth()->user()->account->sx_company_number)->where('status', 'Ignored')->count();

    }

    public function columns(): array
    {
        return [
            Column::make('Order Number', 'order_number')
                ->searchable()->excludeFromColumnSelect()
                ->secondaryHeader($this->getFilterByKey('order_number'))
                ->format(function ($value, $row) {
                    $link = '<a href="'.route('backorder.show', ['orderno' => $value, 'ordersuf' => $row->order_number_suffix]).'" class="text-decoration-underline">'.$value.'-'.$row->order_number_suffix.'</a>';
                    if($row->is_dnr) $link = $link.'<span class="badge bg-light-warning float-end">DNR</span>';
                    return $link;
                })
                ->html(),

            Column::make('Is DNR', 'is_dnr')
                ->hideIf(1)
                ->html(),

            Column::make('Order Number Suffix', 'order_number_suffix')
                ->hideIf(1)
                ->html(),

            Column::make('Warehouse', 'whse')
                ->secondaryHeader($this->getFilterByKey('whse'))
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->html(),

            Column::make('Order Date', 'order_date')
                ->secondaryHeader($this->getFilterByKey('order_date'))
                ->format(function ($value, $row) {
                    return $value->toFormattedDateString().'<span class="badge bg-light-secondary float-end"><i class="fas fa-history"></i> '.$value->diffForHumans().'</span>';
                })
                ->html()
                ->sortable()
                ->excludeFromColumnSelect(),

            Column::make('Taken By', 'taken_by')
                ->secondaryHeader($this->getFilterByKey('operator'))
                ->format(function ($value, $row) {
                    return $this->getTakenByName($value);
                })
                ->excludeFromColumnSelect(),


            Column::make('SX Stage Code', 'stage_code')
                ->format(function ($value, $row) {
                    return '<span class="text-'.$this->getStageCodeClass($value).' fw-bold">'.$this->getStageCode($value).'</span>';
                })
                ->secondaryHeader($this->getFilterByKey('stage_codes'))
                ->html()
                ->excludeFromColumnSelect(),


            Column::make('Portal Status', 'status')
                ->format(function ($value, $row) {
                    return '<span class="badge bg-light-'. $value->class() .'">'. $value->label() .'</span>';
                })
                ->secondaryHeader($this->getFilterByKey('status'))
                ->html()
                ->excludeFromColumnSelect(),
        ];

    }

    public function filters(): array
    {
        return [
            TextFilter::make('Order Number', 'order_number')
            ->hiddenFromMenus()
                ->config([
                    'placeholder' => 'Search Order',
                    'maxlength' => '11',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('order_number', 'like', '%'.$value.'%');
                }),

            SelectFilter::make('DNR Visibility', 'is_dnr')
                ->options([
                    '' => 'All',
                    1 => 'Show only orders with DNR',
                    0 => 'Show only orders with non-DNR',
                    2 => 'Show DNR orders that are Pending Review'
                ])->filter(function (Builder $builder, string $value) {
                    if($value == 2) $builder->where('is_dnr', 1)->where('status', 'Pending Review');
                    else
                    $builder->where('is_dnr', $value);
                }),

            SelectFilter::make('Warehouse', 'whse')
            ->hiddenFromMenus()
                ->options(['' => 'All Warehouses'] + Warehouse::where('cono', auth()->user()->account->sx_company_number)->orderBy('title')->pluck('title', 'short')->toArray())->filter(function (Builder $builder, string $value) {
                    $builder->where(DB::raw('lower(whse)'), strtolower($value));
            }),
            
            DateRangeFilter::make('Order Date', 'order_date')
            ->hiddenFromMenus()
                ->config(['placeholder' => 'Enter Date Range'])
                ->filter(function (Builder $builder, array $dateRange) { // Expects an array.
                    $builder
                        ->whereDate('order_date', '>=', $dateRange['minDate']) // minDate is the start date selected
                        ->whereDate('order_date', '<=', $dateRange['maxDate']); // maxDate is the end date selected
                }),

            SelectFilter::make('Operators', 'operator')
            ->hiddenFromMenus()
                ->options(['' => 'All'] + Operator::where('cono', auth()->user()->account->sx_company_number)->orderBy('name')->pluck('name', 'operator')->toArray())
                ->filter(function (Builder $builder, string $value) {
                    $builder->where(DB::raw('lower(taken_by)'), strtolower($value));
            }),

            SelectFilter::make('Stage Codes', 'stage_codes')
            ->hiddenFromMenus()
                ->options(['' => 'All'] + [
                    0 => 'Quoted',
                    1 => 'Ordered',
                    2 => 'Picked',
                    3 => 'Shipped',
                    4 => 'Invoiced',
                    5 => 'Paid',
                    9 => 'Cancelled',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('stage_code', $value);
            }),

            SelectFilter::make('Status', 'status')
            ->hiddenFromMenus()
                ->options(['' => 'All'] + [
                    'Pending Review' => 'Pending Review',
                    'Ignored' => 'Ignored',
                    'Cancelled' => 'Cancelled',
                    'Follow Up' => 'Follow Up',
                    'Closed' => 'Closed',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('status', $value);
            })

        ];
    }

    public function builder(): Builder
    {
        return Order::where('cono', auth()->user()->account->sx_company_number);
    }

    public function setFilterValue($filter, $value)
    {
        $this->setFilter($filter, $value);
    }

    private function getStageCode($code)
    {
        $stage_codes = [
            0 => 'Quoted',
            1 => 'Ordered',
            2 => 'Picked',
            3 => 'Shipped',
            4 => 'Invoiced',
            5 => 'Paid',
            9 => 'Cancelled',
        ];

        return $stage_codes[$code];
    }

    private function getTakenByName($value)
    {
        if(empty($value)) return 'n/a';

        if(strtolower($value) == 'web') return 'WEB';

        $operator = Operator::where('operator', $value)->first()?->name;

        return $operator ? $operator.' ('.$value.')' : $value;
    }

    private function getStageCodeClass($code)
    {
        $stage_codes = [
            0 => 'warning',
            1 => 'primary',
            2 => 'dark',
            3 => 'secondary',
            4 => 'primary',
            5 => 'success',
            9 => 'danger',
        ];

        return $stage_codes[$code];
    }



}
