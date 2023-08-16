<?php

namespace App\Http\Livewire\Core\Customer;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Account;
use App\Models\Core\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

    public Account $account;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableRowUrl(function ($row) {
                return route('core.customer.show', $row);
            })
            ->setTableRowUrlTarget(function ($row) {
                return '_blank';
            });

        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

        $this->setFilterLayout('slide-down');
        $this->setFilterSlideDownDefaultStatusEnabled();

        $this->setEmptyMessage('No customers found. Use global search to search on all columns and make sure no filters are applied.');

    }

    public function boot(): void
    {
    }

    public function columns(): array
    {
        return [

            Column::make('Id', 'id')
                ->excludeFromColumnSelect()
                ->hideIf(1)
                ->html(),

            Column::make('Name', 'name')
                ->secondaryHeader($this->getFilterByKey('name'))
                ->searchable(function (Builder $query, $searchTerm) {
                    if (str_contains(strtolower($searchTerm), ' and')) {
                        $query->orWhere('name', 'like', '%'.str_replace(' and', ' &', strtolower($searchTerm)).'%')->orWhere('name', 'like', '%'.$searchTerm.'%');
                    }
                    if (str_contains(strtolower($searchTerm), ' &')) {
                        $query->orWhere('name', 'like', '%'.str_replace(' &', ' and', $searchTerm).'%')->orWhere('name', 'like', '%'.$searchTerm.'%');
                    } else {
                        $query->orWhere('name', 'like', '%'.$searchTerm.'%');
                    }
                })
                ->format(function ($value, $row) {
                    $display = '';
                    if ($row->is_active) {
                        $display .= '<span class="badge bg-light-success"><i class="fas fa-user-check"></i> '.ucwords(strtolower($value)).' ('.$row->sx_customer_number.')</span>';
                    } else {
                        $display .= '<span class="badge bg-light-danger"><i class="fas fa-user-times"></i> '.ucwords(strtolower($value)).' ('.$row->sx_customer_number.')</span>';
                    }

                    if ($row->open_order_count > 0) {
                        $display .= '<span class="badge bg-light-warning float-end"><i class="fas fa-exchange-alt"></i> '.$row->open_order_count.'</span>';
                    }

                    return $display;
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Address', 'address')
                ->searchable()
                ->secondaryHeader($this->getFilterByKey('address'))
                ->format(function ($value, $row) {
                    $address = ucwords(strtolower($value));

                    if ($row->address2) {
                        $address .= ', '.ucwords(strtolower($row->address2));
                    }

                    $address .= ', '.ucwords(strtolower($row->city));

                    $address .= ', '.$row->state;

                    $address .= ', '.$row->zip;

                    return $address;

                })
                ->html(),

            Column::make('City', 'city')
                ->searchable()
                ->hideIf(1),

            Column::make('ZIP', 'zip')
                ->searchable()
                ->hideIf(1),

            Column::make('Address 2', 'address2')
                ->searchable()
                ->hideIf(1),

            Column::make('State', 'state')
                ->hideIf(1),

            Column::make('Cust Type', 'customer_type')
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->html(),

            Column::make('Phone', 'phone')
                ->searchable()
                ->secondaryHeader($this->getFilterByKey('phone'))
                ->format(function ($value, $row) {
                    return format_phone($value);
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('E-Mail', 'email')
                ->secondaryHeader($this->getFilterByKey('email'))
                ->searchable()
                ->format(function ($value, $row) {
                    return '<a href="mailto:'.$value.'">'.strtolower($value).'</a>';
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('SX Number', 'sx_customer_number')
                ->searchable()
                ->hideIf(1)
                ->excludeFromColumnSelect(),

            Column::make('Look Up Name', 'look_up_name')
                ->hideIf(1)
                ->searchable(),

            Column::make('Sales Terr', 'sales_territory'),

            Column::make('Customer Since', 'customer_since')
                ->deselected()
                ->format(function ($value) {
                    if ($value) {
                        return $value->format('M Y');
                    }
                }),

            Column::make('Last Sale Date', 'last_sale_date')
                ->excludeFromColumnSelect()
                ->format(function ($value) {
                    if ($value) {
                        return date('M d, Y', strtotime($value));
                    }
                }),

            Column::make('Open Order', 'open_order_count')
                ->excludeFromColumnSelect()
                ->hideIf(1),

            Column::make('Active', 'is_active')
                ->hideIf(1),

        ];
    }

    public function filters(): array
    {
        return [

            TextFilter::make('Name')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Name / SX#',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if (str_contains(strtolower($value), ' and')) {
                        $builder->where('name', 'like', '%'.str_replace(' and', ' &', strtolower($value)).'%')->orWhere('name', 'like', '%'.$value.'%');
                    }
                    if (str_contains(strtolower($value), ' &')) {
                        $builder->where('name', 'like', '%'.str_replace(' &', ' and', $value).'%')->orWhere('name', 'like', '%'.$value.'%');
                    }
                    if (is_numeric($value)) {
                        $builder->where('sx_customer_number', '=', $value);
                    } else {
                        $builder->where('name', 'like', '%'.$value.'%');
                    }
                }),

            TextFilter::make('Address')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Address',
                    'maxlength' => '50',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('address', 'like', '%'.$value.'%')
                        ->orWhere('address2', 'like', '%'.$value.'%');
                }),

            TextFilter::make('Phone')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Phone',
                    'maxlength' => '15',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('phone', 'like', '%'.preg_replace("/[^0-9]/", "", $value).'%');
                }),

            TextFilter::make('Email')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Email',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('email', 'like', '%'.$value.'%');
                }),

            SelectFilter::make('Customer Order Status')
                ->options([
                    '' => 'Show All',
                    'open-orders' => 'Show all with Open Orders',
                    'open-orders-multiple' => 'Show all with Multiple Open Orders',
                ])->filter(function (Builder $builder, string $value) {
                    if ($value == 'open-orders') {
                        $builder->where('open_order_count', '>',0);
                    }
                    if ($value == 'open-orders-multiple') {
                        $builder->where('open_order_count','>', 1);
                    }
                }),
            SelectFilter::make('Customer Status')
                ->options([
                    '' => 'All',
                    1 => 'Active',
                    0 => 'In-Active',
                ])->filter(function (Builder $builder, string $value) {
                    if ($value == 1) {
                        $builder->where('is_active', 1);
                    }
                    if ($value == 0) {
                        $builder->where('is_active', 0);
                    }
                }),
            SelectFilter::make('Customer Type')
                ->options([
                    '' => 'All',
                    'HOM' => 'HOM',
                    'LAN' => 'LAN',
                    'SPC' => 'SPC',
                    'EMP' => 'EMP',
                    'WEB' => 'WEB',
                    'MUN' => 'MUN',
                ])->filter(function (Builder $builder, string $value) {
                    $builder->where('customer_type', $value);
                }),
        ];
    }

    public function builder(): Builder
    {
        return Customer::where('account_id', $this->account->id)
            ->orderBy('open_order_count', 'DESC')
            ->orderBy('name', 'ASC')
            ->orderBy('last_sale_date', 'DESC');
    }
}
