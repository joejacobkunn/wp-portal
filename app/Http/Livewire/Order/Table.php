<?php

namespace App\Http\Livewire\Order;

use App\Enums\Order\OrderStatus;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Operator;
use App\Models\Core\User;
use App\Models\Order\DnrBackorder;
use App\Models\Order\Order;
use App\Models\Core\Warehouse;
use Carbon\Carbon;
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

    public $enableEcomZwhs = false;

    public $warehouses = [];

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
        //$this->setFilterLayout('slide-down');
        $this->setConfigurableAreas([
            'toolbar-right-start' => 'livewire.order.partials.settings-table-btn',
            ]);
    


    }

    /**
     * Dynamic listener definitions
     */
    public function getListeners()
    {
        $listeners = $this->listeners;

        $listeners = array_merge($listeners, [
            'order-table:filter' => 'setFilterValue',
        ]);

        return $listeners;
    }

    public function boot(): void
    {
        $this->authorize('viewAny', Order::class);
    }

    public function mount()
    {
        $this->setFilter('stage_codes', 'open');
        $this->warehouses = Warehouse::where('cono', auth()->user()->account->sx_company_number)->orderBy('title')->pluck('title', 'short')->toArray();


    }

    public function columns(): array
    {
        return [
            Column::make('Order Number', 'order_number')
                ->searchable()->excludeFromColumnSelect()
                ->secondaryHeader($this->getFilterByKey('order_number'))
                ->format(function ($value, $row) {
                    $link = '<a href="'.route('order.show', $row->id).'" class="text-decoration-underline" wire:navigate>'.$value.'-'.$row->order_number_suffix.'</a>';
                    if($row->is_dnr) $link = $link.'<span class="badge bg-light-danger float-end">DNR</span>';
                    if($row->qty_ord > $row->qty_ship) $link = $link.'<span class="badge bg-light-warning float-end">BACKORDER</span>';
                    if($row->is_sro) $link = $link.'<span class="badge bg-light-info float-end">SRO</span>';
                    if($row->warehouse_transfer_available) $link = $link.'<span class="badge bg-light-primary float-end">WT</span>';
                    if($row->partial_warehouse_transfer_available) $link = $link.'<span class="badge bg-light-primary float-end">P-WT</span>';
                    if(!empty($row->golf_parts)) $link = $link.'<span class="badge bg-light-primary float-end">GOLF</span>';
                    if(!empty($row->non_stock_line_items)) $link = $link.'<span class="badge bg-light-warning float-end">Non Stock</span>';
                    return $link;
                })
                ->html(),

            Column::make('Id', 'id')
                ->hideIf(1)
                ->html(),

                Column::make('Golf Parts', 'golf_parts')
                ->hideIf(1)
                ->html(),

                Column::make('Non Stock', 'non_stock_line_items')
                ->hideIf(1)
                ->html(),


            Column::make('Warehouse Transfer', 'warehouse_transfer_available')
                ->hideIf(1)
                ->html(),

                Column::make('Partial Warehouse Transfer', 'partial_warehouse_transfer_available')
                ->hideIf(1)
                ->html(),




            Column::make('Is DNR', 'is_dnr')
                ->hideIf(1)
                ->html(),

            Column::make('Is SRO', 'is_sro')
                ->hideIf(1)
                ->html(),

            Column::make('Quantity Ordered', 'qty_ord')
                ->hideIf(1)
                ->html(),

            Column::make('Quantity Shipped', 'qty_ship')
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
                    return strtoupper($value);
                })
                ->html(),

            Column::make('Order Date', 'order_date')
                ->secondaryHeader($this->getFilterByKey('order_date'))
                ->format(function ($value, $row) {
                    return $value?->toFormattedDateString().'<span class="badge bg-light-secondary float-end"><i class="fas fa-history"></i> '.$value->diffForHumans().'</span>';
                })
                ->html()
                ->sortable()
                ->excludeFromColumnSelect(),

            Column::make('Last Line Entered Date', 'last_line_added_at')
                ->format(function ($value, $row) {
                    return (!is_null($value)) ? $value?->toFormattedDateString() : '';
                })
                ->html()
                ->sortable(),

            Column::make('Promise Date', 'promise_date')
                ->secondaryHeader($this->getFilterByKey('promise_date'))
                ->format(function ($value, $row) {
                    return $value?->toFormattedDateString();
                })
                ->html()
                ->sortable()
                ->excludeFromColumnSelect(),

            Column::make('Last Followed Up', 'last_followed_up_at')
                ->secondaryHeader($this->getFilterByKey('last_followed_up_at'))
                ->format(function ($value, $row) {
                    return $value?->toFormattedDateString();
                })
                ->html()
                ->sortable(),


            Column::make('Taken By', 'taken_by')
                ->secondaryHeader($this->getFilterByKey('operator'))
                ->format(function ($value, $row) {
                    return $this->getTakenByName($value);
                })
                ->excludeFromColumnSelect(),

            Column::make('Ship Via', 'ship_via')
                ->secondaryHeader($this->getFilterByKey('ship_via'))
                ->format(function ($value, $row) {
                    return $value;
                })
                ->html(),



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
                    1 => 'DNR',
                    0 => 'Non-DNR',
                    2 => 'DNR Pending Review'
                ])->filter(function (Builder $builder, string $value) {
                    if($value == 2) $builder->where('is_dnr', 1)->where('status', 'Pending Review');
                    else
                    $builder->where('is_dnr', $value);
                }),

                SelectFilter::make('Follow Up Visibility', 'is_follow_up')
                ->options([
                    '' => 'All',
                    0 => 'All Follow Ups',
                    1 => 'Customer',
                    2 => 'Shipment',
                    3 => 'Receiving'
                ])->filter(function (Builder $builder, string $value) {
                    if($value == 0) $builder->whereIn('status', ['Follow Up', 'Shipment Follow Up', 'Receiving Follow Up']);
                    if($value == 1) $builder->where('status', 'Follow Up');
                    if($value == 3) $builder->where('status', 'Receiving Follow Up');
                }),

                SelectFilter::make('Ship Via', 'ship_via')
                ->hiddenFromMenus()
                ->options([
                    '' => 'All',
                    'pkup' => 'PKUP',
                    'u11' => 'U11',
                    'sro' => 'SRO',
                    'will' => 'WILL'
                ])->filter(function (Builder $builder, string $value) {
                    $builder->where(DB::raw('lower(ship_via)'), strtolower($value));
                }),

                SelectFilter::make('Order Status', 'order_standing')
                ->options([
                    '' => 'All',
                    'backorder' => 'Backorders',
                    'completed' => 'Completed',
                    'wt' => 'WT Availability',
                    'p-wt' => 'WT Partial',
                    'non-stock' => 'Non-Stock'
                ])->filter(function (Builder $builder, string $value) {
                    if($value == 'backorder') $builder->whereColumn('qty_ord','>','qty_ship')->where('last_line_added_at', '<', Carbon::today());
                    if($value == 'completed') $builder->whereColumn('qty_ord','=','qty_ship');
                    if($value == 'wt') $builder->where('warehouse_transfer_available','=',1);
                    if($value == 'p-wt') $builder->where('partial_warehouse_transfer_available','=',1);
                    if($value == 'non-stock') $builder->where('non_stock_line_items','<>',null);
                }),

                
                SelectFilter::make('Order Type', 'order_type')
                ->options([
                    '' => 'All',
                    'sro' => 'SRO',
                    'web' => 'Web',
                    'sales' => 'Sales',
                    'in-store' => 'Parts In-Store',
                    'golf' => 'Golf (WEB)'
                ])->filter(function (Builder $builder, string $value) {
                    if($value == 'web') $builder->where('is_web_order',1);
                    if($value == 'sro') $builder->where('is_sro','=',1);
                    if($value == 'sales') $builder->where('is_sales_order',1)->whereNot('is_sro',1);
                    if($value == 'in-store') $builder->where('is_sales_order',0)->where('is_sro', 0)->where('taken_by', '<>', 'web');
                    if($value == 'golf') $builder->where('golf_parts', '<>', null);
                }),

            MultiSelectDropdownFilter::make('Warehouse', 'whse')
                ->hiddenFromMenus()
                ->options($this->warehouses)
                ->filter(function (Builder $builder, array $values) {
                        $builder->whereIn(DB::raw('lower(whse)'), $values);
                    }
                ),
            
            DateRangeFilter::make('Order Date', 'order_date')
            ->hiddenFromMenus()
                ->config(['placeholder' => 'Enter Date Range'])
                ->filter(function (Builder $builder, array $dateRange) { // Expects an array.
                    $builder
                        ->whereDate('order_date', '>=', $dateRange['minDate']) // minDate is the start date selected
                        ->whereDate('order_date', '<=', $dateRange['maxDate']); // maxDate is the end date selected
                }),

                SelectFilter::make('Promise Date', 'promise_date')
                ->hiddenFromMenus()
                    ->options(['' => 'All'] + [
                        'past_due' => 'Past Due',
                        'unknown' => 'Unknown',
                        'two_weeks_plus' => '2+ Weeks',
                        'less_than_two_weeks' => '<2 Weeks'
                    ])
                    ->filter(function (Builder $builder, string $value) {
                        if($value == 'past_due') $builder->where('promise_date', '<',Carbon::today());
                        if($value == 'unknown') $builder->where('promise_date', '2049-01-01');
                        if($value == 'two_weeks_plus') $builder->where('promise_date', '>', Carbon::now()->addWeek(2))->where('promise_date', '<>', '2049-01-01');
                        if($value == 'less_than_two_weeks') $builder->whereBetween('promise_date', [Carbon::yesterday()->format('Y-m-d'),Carbon::now()->addWeek(2)])->where('promise_date', '<>', '2049-01-01');
                }),
    
    

            SelectFilter::make('Operators', 'operator')
            ->hiddenFromMenus()
                ->options(['' => 'All'] + Operator::where('cono', auth()->user()->account->sx_company_number)->orderBy('name')->get()->pluck('full_name', 'operator')->toArray())
                ->filter(function (Builder $builder, string $value) {
                    $builder->where(DB::raw('lower(taken_by)'), strtolower($value));
            }),

            SelectFilter::make('Stage Code', 'stage_codes')
            ->hiddenFromMenus()
                ->options([
                    'open' => 'Open',
                    'closed' => 'Closed',
                    'cancelled' => 'Cancelled',
                    'quotes' => 'Quotes',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if($value == 'open') $builder->whereIn('stage_code', [1,2]);
                    if($value == 'closed') $builder->whereIn('stage_code', [3,4,5]);
                    if($value == 'cancelled') $builder->where('stage_code', 9);
                    if($value == 'quotes') $builder->where('stage_code', 0);
            }),

            SelectFilter::make('Last Follow Up', 'last_followed_up_at')
            ->hiddenFromMenus()
                ->options(['' => 'All'] + [
                    'today' => 'Today',
                    'yesterday' => 'Yesterday',
                    'this_week' => 'This Week',
                    'older_two_weeks' => 'Older than Two Weeks',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if($value == 'today')
                    {
                        $builder->whereDate('last_followed_up_at', Carbon::today());
                    }
                    if($value == 'yesterday')
                    {
                        $builder->whereDate('last_followed_up_at', Carbon::yesterday());
                    }
                    if($value == 'this_week')
                    {
                        $builder->whereBetween('last_followed_up_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    }
                    if($value == 'older_two_weeks')
                    {
                        $builder->where('last_followed_up_at', '<', Carbon::now()->subWeek(2));
                    }
            }),

            SelectFilter::make('Status', 'status')
            ->hiddenFromMenus()
                ->options(['' => 'All'] + [
                    'Pending Review' => 'Pending Review',
                    'Ignored' => 'Ignored',
                    'Cancelled' => 'Cancelled',
                    'Follow Up' => 'Follow Up',
                    'Shipment Follow Up' => 'Shipment Follow Up',
                    'Receiving Follow Up' => 'Receiving Follow Up',
                    'Closed' => 'Closed',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('status', $value);
            })

        ];
    }

    public function builder(): Builder
    {
        //dd($this->enableEcomZwhs);
        if($this->enableEcomZwhs)
        {
            $this->warehouses = $this->warehouses + ['ecom' => 'ECOM','zwhs' => 'ZWHS'];
        }else{
            $this->warehouses = array_diff($this->warehouses, ['ecom' => 'ECOM','zwhs' => 'ZWHS']);
        }


        $query = Order::where('cono', auth()->user()->account->sx_company_number)->whereIn('whse', array_keys($this->warehouses));

        return $query;
    }

    public function setFilterValue($filter, $value)
    {
        $this->setFilterDefaults();
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
