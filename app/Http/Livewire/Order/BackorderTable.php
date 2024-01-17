<?php

namespace App\Http\Livewire\Order;

use App\Enums\Order\BackOrderStatus;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\User;
use App\Models\Order\DnrBackorder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectDropdownFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class BackorderTable extends DataTableComponent
{
    use AuthorizesRequests;

    public $activeTab;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function boot(): void
    {

    }

    public function columns(): array
    {
        return [
            Column::make('Order Number', 'order_number')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('backorder.show', ['orderno' => $value, 'ordersuf' => $row->order_number_suffix]).'" class="text-decoration-underline">'.$value.'-'.$row->order_number_suffix.'</a>';
                })
                ->html(),

            Column::make('Order Number Suffix', 'order_number_suffix')
                ->hideIf(1)
                ->html(),

            Column::make('WHSE', 'whse')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->html(),

            Column::make('Order Date', 'order_date')
                ->format(function ($value, $row) {
                    return $value->toFormattedDateString();
                })
                ->sortable()
                ->excludeFromColumnSelect(),

            Column::make('Status', 'status')
                ->format(function ($value, $row) {
                    return '<span class="badge bg-light-'. $value->class() .'">'. $value->label() .'</span>';
                })
                ->html()
                ->excludeFromColumnSelect(),


        ];
    }

    public function filters(): array
    {
        return  [
            MultiSelectFilter::make('Status')
                ->options(
                    [
                        'Pending Review',
                        'Ignored',
                        'Cancelled'
                    ]
                )->filter(function(Builder $builder, string $value) {
                    $builder->whereIn('status', $value);
                })
        ];
    }

    public function builder(): Builder
    {
        $query = DnrBackorder::where('cono', auth()->user()->account->sx_company_number);

        switch ($this->activeTab) {
            case 'PendingReview':
                $query->where('status', BackOrderStatus::PendingReview->value);
                break;

            case 'ignored':
                $query->where('status', BackOrderStatus::Ignore->value);
                break;

            case 'cancelled':
                $query->where('status', BackOrderStatus::Cancelled->value);
                break;

            case 'Closed':
                $query->where('status', 'Closed');
                break;
        }

        return $query;
    }
}
