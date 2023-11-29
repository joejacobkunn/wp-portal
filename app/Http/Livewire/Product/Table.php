<?php

namespace App\Http\Livewire\Product;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Product\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

    public Account $account;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        //     ->setTableRowUrl(function ($row) {
        //         return route('core.customer.show', $row);
        //     })
        //     ->setTableRowUrlTarget(function ($row) {
        //         return '_blank';
        //     });

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

            Column::make('PROD', 'prod')
                ->secondaryHeader($this->getFilterByKey('prod'))
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Description', 'description')
                ->searchable()
                ->secondaryHeader($this->getFilterByKey('description'))
                ->html(),
            
                Column::make('List Price', 'list_price')
                ->format(function ($value, $row) {
                    return '$'.number_format($value);
                })
                ->html(),


                Column::make('Look Up Name', 'look_up_name')
                ->searchable()
                ->secondaryHeader($this->getFilterByKey('look_up_name'))
                ->html(),


            Column::make('Brand', 'brand')
                ->secondaryHeader($this->getFilterByKey('brand'))
                ->searchable(),

            Column::make('Vend No', 'vend_no')
                ->searchable(),

            Column::make('Vendor', 'vendor')
                ->searchable(),

            Column::make('Category', 'category')
                ->secondaryHeader($this->getFilterByKey('category'))
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->html(),

            Column::make('Product Line', 'product_line')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Entered Date', 'entered_date')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Last Sold Date', 'last_sold_date')
                ->searchable()
                ->excludeFromColumnSelect(),

            Column::make('Active', 'active')
                ->excludeFromColumnSelect(),

                Column::make('Status', 'status')
                ->excludeFromColumnSelect(),


        ];
    }

    public function filters(): array
    {
        return [

            TextFilter::make('prod')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Prod',
                    'maxlength' => '25',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('prod', 'like', '%'.$value.'%');
                }),

            TextFilter::make('description')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Description',
                    'maxlength' => '50',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('description', 'like', '%'.$value.'%');
                }),

            TextFilter::make('look_up_name')
                ->hiddenFromAll()
                ->config([
                    'placeholder' => 'Search Lookup',
                    'maxlength' => '50',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('look_up_name', 'like', '%'.$value.'%');
                }),

            SelectFilter::make('brand')
                ->hiddenFromAll()
                ->options(['' => 'All Brands'] + Product::orderBy('brand', 'asc')->pluck('brand','brand')->unique()->toArray())->filter(function (Builder $builder, string $value) {
                    $builder->where('brand', $value);
                }),

            SelectFilter::make('category')
                ->hiddenFromAll()
                ->options(['' => 'All Categories'] + Product::orderBy('category', 'asc')->pluck('category','category')->unique()->toArray())->filter(function (Builder $builder, string $value) {
                    $builder->where('category', $value);
                }),



        ];
    }

    public function builder(): Builder
    {
        return Product::where('account_id', $this->account->id)
            ->without('account')
            ->orderBy('last_sold_date', 'DESC');
    }
}
