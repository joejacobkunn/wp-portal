<?php

namespace App\Http\Livewire\Order;

use App\Enums\Order\OrderStatus;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Operator;
use App\Models\Core\User;
use App\Models\Order\DnrBackorder;
use App\Models\Order\Order;
use App\Models\Core\Warehouse;
use App\Models\Order\OrderFilterCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    public $filteredRowCount;

    public $warehouses = [];

    public $isFilterSaved;

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
            'toolbar-left-end' => 'livewire.order.partials.result-count',

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
       // $this->setFilter('stage_codes', 'open');
        $this->warehouses = Warehouse::where('cono', auth()->user()->account->sx_company_number)->orderBy('title')->pluck('title', 'short')->toArray();

        $this->isFilterSaved = auth()->user()->orderFilterCache?->status ?? false;
        if($this->isFilterSaved) {
            $filters = auth()->user()->orderFilterCache->filters;
            foreach ($filters as $key => $value) {
                $this->setFilter($key, $value);
            }
        }
    }

    public function columns(): array
    {
        return [
            Column::make('Order Number', 'order_number')
                ->searchable()->excludeFromColumnSelect()
                ->secondaryHeader($this->getFilterByKey('order_number'))
                ->format(function ($value, $row) {
                    $link = '<a href="'.route('order.show', $row->id).'" class="text-decoration-underline" wire:navigate>'.$value.'-0'.$row->order_number_suffix.'</a>';
                    if($row->is_dnr) $link = $link.'<span class="badge bg-light-danger float-end">DNR</span>';
                    if($row->qty_ord > $row->qty_ship) $link = $link.'<span class="badge bg-light-warning float-end">BACKORDER</span>';
                    if($row->is_sro) $link = $link.'<span class="badge bg-light-info float-end">SRO</span>';
                    if($row->warehouse_transfer_available) $link = $link.'<span class="badge bg-light-primary float-end">WT</span>';
                    if($row->partial_warehouse_transfer_available) $link = $link.'<span class="badge bg-light-primary float-end">P-WT</span>';
                    if(!empty($row->golf_parts)) $link = $link.'<span class="badge bg-light-primary float-end">GOLF</span>';
                    if(!empty($row->non_stock_line_items)) $link = $link.'<span class="badge bg-light-danger float-end">NON-STOCK</span>';
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

                MultiSelectDropdownFilter::make('DNR Visibility', 'is_dnr')
                ->options([
                    1 => 'DNR',
                    0 => 'Non-DNR',
                    2 => 'DNR Pending Review'
                ])->filter(function (Builder $builder, $value) {
                    $builder->where(function ($query) use ($value) {
                        $conditions = [];

                        if (in_array(2, $value)) {
                            $conditions[] = ['is_dnr', '=', 1];
                            $conditions[] = ['status', '=', 'Pending Review'];
                        }
                        if (in_array(1, $value) && !in_array(2, $value)) {
                            $conditions[] = ['is_dnr', '=', 1];
                        }
                        if (in_array(0, $value)) {
                            $conditions[] = ['is_dnr', '=', 0];
                        }

                        if (count($conditions) > 0) {
                            foreach ($conditions as $condition) {
                                $query->orWhere(...$condition);
                            }
                        }
                    });
                }),

                MultiSelectDropdownFilter::make('Follow Up Visibility', 'is_follow_up')
                ->options([
                    1 => 'Customer',
                    2 => 'Shipment',
                    3 => 'Receiving'
                ])->filter(function (Builder $builder, $value) {
                    if(in_array(1, $value)) $searchKey[] = 'Follow Up';
                    if(in_array(2, $value)) $searchKey[] = 'Shipment Follow Up';
                    if(in_array(3, $value)) $searchKey[] = 'Receiving Follow Up';
                    $builder->whereIn('status', array_unique($searchKey));
                }),

                MultiSelectDropdownFilter::make('Ship Via', 'ship_via')
                ->hiddenFromMenus()
                ->options([

                    'pkup' => 'PKUP',
                    'u11' => 'U11',
                    'sro' => 'SRO',
                    'will' => 'WILL'
                ])->filter(function (Builder $builder, $value) {
                    $searchKey = [];
                    if($value == 'pkup') $searchKey = array_merge($searchKey, ['pkfh','pkcl','pkcs','pkaa', 'pkut','pkli']);
                    $searchKey = array_merge($searchKey, $value);
                    $builder->whereIn(DB::raw('lower(ship_via)'), array_unique($searchKey));
                }),

                MultiSelectDropdownFilter::make('Order Status', 'order_standing')
                ->options([
                    'backorder' => 'Backorders',
                    'completed' => 'Completed',
                    'wt' => 'WT Availability',
                    'p-wt' => 'WT Partial',
                    'non-stock' => 'Non-Stock'
                ])->filter(function (Builder $builder, array $value) {

                    $builder->where(function ($query) use ($value) {

                        if (in_array('backorder', $value)) {
                            $query->whereColumn('qty_ord', '>', 'qty_ship')
                                  ->where('last_line_added_at', '<', Carbon::today());
                        }

                        if (in_array('completed', $value)) {
                            $query->orWhereColumn('qty_ord', '=', 'qty_ship');
                        }

                        if (in_array('wt', $value)) {
                            $query->orWhere('warehouse_transfer_available', '=', 1);
                        }

                        if (in_array('p-wt', $value)) {
                            $query->orWhere('partial_warehouse_transfer_available', '=', 1);
                        }

                        if (in_array('non-stock', $value)) {
                            $query->orWhere('non_stock_line_items', '<>', null);
                        }
                    });
                }),

                MultiSelectDropdownFilter::make('Order Type', 'order_type')
                ->options([
                    'sro' => 'SRO',
                    'web' => 'Web',
                    'sales' => 'Sales',
                    'in-store' => 'Parts In-Store',
                    'golf' => 'Golf (WEB)'
                ])->filter(function (Builder $builder, array $value) {

                    $builder->where(function ($query) use ($value) {

                        if (in_array('web', $value)) {
                            $query->where('is_web_order', 1);
                        }

                        if (in_array('sro', $value)) {
                            $query->orWhere('is_sro', '=', 1);
                        }

                        if (in_array('sales', $value)) {
                            $query->orWhere(function ($subQuery) {
                                $subQuery->where('is_sales_order', '=', 1)
                                         ->whereNot('is_sro', 1);
                            });
                        }

                        if (in_array('in-store', $value)) {
                            $query->orWhere(function ($subQuery) {
                                $subQuery->where('is_sales_order', '=', 0)
                                         ->where('is_sro', '=', 0)
                                         ->where('taken_by', '<>', 'web');
                            });
                        }

                        if (in_array('golf', $value)) {
                            $query->orWhere('golf_parts', '<>', null);
                        }
                    });
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

                MultiSelectDropdownFilter::make('Promise Date', 'promise_date')
                ->hiddenFromMenus()
                    ->options([
                        'past_due' => 'Past Due',
                        'unknown' => 'Unknown',
                        'two_weeks_plus' => '2+ Weeks',
                        'less_than_two_weeks' => '<2 Weeks'
                    ])
                    ->filter(function (Builder $builder, $value) {
                        $builder->where(function ($query) use ($value) {
                            $conditions = [];

                            if (in_array('past_due', $value)) {
                                $conditions[] = ['promise_date', '<', Carbon::today()];
                            }
                            if (in_array('unknown', $value)) {
                                $conditions[] = ['promise_date', '=', '2049-01-01'];
                            }
                            if (in_array('two_weeks_plus', $value)) {
                                $conditions[] = ['promise_date', '>', Carbon::now()->addWeek(2)];
                                $conditions[] = ['promise_date', '<>', '2049-01-01'];
                            }
                            if (in_array('less_than_two_weeks', $value)) {
                                $conditions[] = ['promise_date', '>=', Carbon::yesterday()->format('Y-m-d')];
                                $conditions[] = ['promise_date', '<=', Carbon::now()->addDay(13)];
                                $conditions[] = ['promise_date', '<>', '2049-01-01'];
                            }
                            if (count($conditions) > 0) {
                                foreach ($conditions as $condition) {
                                    $query->orWhere(...$condition);
                                }
                            }
                        });
                    }),

            MultiSelectDropdownFilter::make('Operators', 'operator')
            ->hiddenFromMenus()
            ->options(Operator::where('cono', auth()->user()->account->sx_company_number)->orderBy('name')->get()->pluck('full_name', 'operator')->toArray())
            ->filter(function (Builder $builder, $value) {
                    $builder->whereIn(DB::raw('lower(taken_by)'), array_map('strtolower', $value));
            }),

            MultiSelectDropdownFilter::make('Stage Code', 'stage_codes')
            ->hiddenFromMenus()
                ->options([
                    'open' => 'Open',
                    'closed' => 'Closed',
                    'cancelled' => 'Cancelled',
                    'quotes' => 'Quotes',
                ])
                ->filter(function (Builder $builder, $value) {
                    $searchKey =[];
                    if(in_array('open', $value)) $searchKey = array_merge($searchKey, [1,2]);
                    if(in_array('closed', $value)) $searchKey = array_merge($searchKey, [3,4,5]);
                    if(in_array('cancelled', $value)) $searchKey[] = 9;
                    if(in_array('quotes', $value)) $searchKey[] = 0;

                    $builder->whereIn('stage_code', $searchKey);
            }),

            MultiSelectDropdownFilter::make('Last Follow Up', 'last_followed_up_at')
            ->hiddenFromMenus()
                ->options( [
                    'today' => 'Today',
                    'yesterday' => 'Yesterday',
                    'this_week' => 'This Week',
                    'older_two_weeks' => 'Older than Two Weeks',
                ])
                ->filter(function (Builder $builder, $value) {
                    $builder->where(function ($query) use ($value) {
                        $conditions = [];

                        if (in_array('today', $value)) {
                            $conditions[] = ['last_followed_up_at', '=', Carbon::today()->toDateString()];
                        }
                        if (in_array('yesterday', $value)) {
                            $conditions[] = ['last_followed_up_at', '=', Carbon::yesterday()->toDateString()];
                        }
                        if (in_array('this_week', $value)) {
                            $conditions[] = ['last_followed_up_at', '>=', Carbon::now()->startOfWeek()->toDateString()];
                            $conditions[] = ['last_followed_up_at', '<=', Carbon::now()->endOfWeek()->toDateString()];
                        }
                        if (in_array('older_two_weeks', $value)) {
                            $conditions[] = ['last_followed_up_at', '<', Carbon::now()->subWeeks(2)->toDateString()];
                        }

                        if (count($conditions) > 0) {
                            foreach ($conditions as $condition) {
                                $query->orWhere(...$condition);
                            }
                        }
                    });
            }),

            MultiSelectDropdownFilter::make('Status', 'status')
            ->hiddenFromMenus()
                ->options([
                    'Pending Review' => 'Pending Review',
                    'Ignored' => 'Ignored',
                    'Cancelled' => 'Cancelled',
                    'Follow Up' => 'Follow Up',
                    'Shipment Follow Up' => 'Shipment Follow Up',
                    'Receiving Follow Up' => 'Receiving Follow Up',
                    'Closed' => 'Closed',
                ])
                ->filter(function (Builder $builder, $value) {
                    $builder->whereIn('status', $value);
            }),

            TextFilter::make('Search by Part', 'line_items')
                ->config([
                    'placeholder' => 'Search Parts',
                    'maxlength' => '20',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereJsonContains('line_items->line_items', ['shipprod' => strtoupper($value)])
                    ->orWhereJsonContains('line_items->line_items', ['shipprod' => strtolower($value)]);
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
        $this->setFilterCountInitLoad($query->count());
        return $query;
    }

    public function setFilterCountInitLoad($total)
    {
        if(!$this->filteredRowCount) {
            $this->filteredRowCount = $total;
        }
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

    public function updatedFilterComponents()
    {
        $this->filteredRowCount = $this->getRows()->total();
        $this->saveFilter();
    }

    public function setFilterDefaults(): void
    {
        foreach ($this->getFilters() as $filter) {
            if ($filter->isResetByClearButton()) {
                $this->resetFilter($filter);
            }
        }
        $this->filteredRowCount = $this->getRows()->total();
        $this->saveFilter();

    }

    public function saveFilter()
    {
        $filters = $this->getAppliedFilters();
        $user = Auth::user();
        $orderFilterCache = OrderFilterCache::updateOrCreate(
            ['user_id' => $user->id],
            [
                'filters' => $filters,
                'status' => $this->isFilterSaved
            ]
        );

    }
}
