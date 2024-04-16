<?php

namespace App\Http\Livewire\Product;

use App\Models\Core\Account;
use App\Models\Core\Customer;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Line;
use App\Models\Product\Product;
use App\Models\Product\Vendor;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;
use Illuminate\Support\Str;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

    public Account $account;

    //the page loaded from pos checkout
    public $fromCheckout = false;

    public $brands = [];
    public $categories = [];
    public $vendors = [];
    public $productLines = [];
    public $cartInProgress = false;

    protected $listeners = [
        'product:table:addToCart' => 'addToCart',
        'pos:addedToCart' => 'cartProcessed',
    ];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        //     ->setTableRowUrl(function ($row) {
        //         return route('core.customer.show', $row);
        //     })
        //     ->setTableRowUrlTarget(function ($row) {
        //         return '_blank';
        //     });

        $paginationOptions = [25, 50, 100];
        if ($this->fromCheckout) {
            array_unshift($paginationOptions, 10);
        }

        $this->setPerPageAccepted($paginationOptions);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

        $this->setFilterLayout('slide-down');

        $this->setEmptyMessage('No customers found. Use global search to search on all columns and make sure no filters are applied.');

    }
    
    public function mount()
    {
        $this->brands = Brand::orderBy('name', 'asc')->pluck('name','id')->toArray();
        $this->categories = Category::orderBy('name', 'asc')->pluck('name','id')->toArray();
        $this->vendors = Vendor::orderBy('name', 'asc')->pluck('name','id')->toArray();
        $this->productLines = Line::orderBy('name', 'asc')->pluck('name','id')->toArray();
    }

    public function columns(): array
    {
        $columns = [

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
                    return is_numeric($value) ? number_format($value,2) : $value;
                })
                ->html(),


                Column::make('Look Up Name', 'look_up_name')
                ->searchable()
                ->secondaryHeader($this->getFilterByKey('look_up_name'))
                ->html(),


            Column::make('Brand', 'brand.name')
                ->secondaryHeader($this->getFilterByKey('brand'))
                ->searchable(),


            Column::make('Vendor', 'vendor.name')
            ->secondaryHeader($this->getFilterByKey('vendor'))

                ->format(function ($value, $row) {
                    $attribute = 'vendor.vendor_number';
                    return $value.'('.$row->$attribute.')';
                })
                ->searchable(),

            Column::make('Vendor', 'vendor.vendor_number')
                ->hideIf(1)
                ->searchable(),


            Column::make('Category', 'category.name')
                ->secondaryHeader($this->getFilterByKey('category'))
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->html(),

            Column::make('Product Line', 'line.name')
            ->secondaryHeader($this->getFilterByKey('line'))
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
                ->format(function ($value, $row) {
                    return Str::headline($value);
                })
                ->excludeFromColumnSelect(),

                Column::make('Status', 'status')
                ->format(function ($value, $row) {
                    return Str::headline($value);
                })
                ->excludeFromColumnSelect(),
        ];


        if ($this->fromCheckout) {
            array_unshift($columns , Column::make('Action', 'id')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return '<button class="btn btn-primary btn-sm text-nowrap" type="button" ' . ($this->cartInProgress ? 'disabled' : '') . ' wire:click="addToCart('. $value .')">Add To Cart</button>';
                })
                ->excludeFromColumnSelect()
                ->html());
        }

        return $columns;
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
                ->options(['' => 'All Brands'] + $this->brands)->filter(function (Builder $builder, string $value) {
                    $builder->where('products.brand_id', $value);
                }),

            SelectFilter::make('category')
                ->options(['' => 'All Categories'] + $this->categories)->filter(function (Builder $builder, string $value) {
                    $builder->where('products.category_id', $value);
                }),

            SelectFilter::make('vendor')
                ->hiddenFromAll()
                ->options(['' => 'All Vendors'] + $this->vendors)->filter(function (Builder $builder, string $value) {
                    $builder->where('products.vendor_id', $value);
                }),

            SelectFilter::make('line')
                ->hiddenFromAll()
                ->options(['' => 'All Product Lines'] + $this->productLines)->filter(function (Builder $builder, string $value) {
                    $builder->where('products.product_line_id', $value);
                }),




        ];
    }

    public function builder(): Builder
    {
        $productQuery = Product::where('account_id', $this->account->id)
            ->with('vendor:name')
            ->with('brand:name')
            ->with('category:name')
            ->with('line:name')
            ->without('account')
            ->orderBy('last_sold_date', 'DESC');

        return $productQuery;
    }

    public function addToCart($prodId)
    {
        $this->cartInProgress = true;
        $this->dispatch('product:cart:selected', $prodId);
    }

    public function cartProcessed()
    {
        $this->cartInProgress = false;
    }

    public function placeholder()
    {
        return view('components.skelton', ['type' => 'table']);
    }
}
