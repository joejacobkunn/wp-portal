<?php

namespace App\Http\Livewire\Core\Customer;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\SRO\Customer as SROCustomer;
use App\Models\SRO\RepairOrders;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class RepairOrdersTable extends DataTableComponent
{
    use AuthorizesRequests;

    public SROCustomer $customer;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
        ->setTableRowUrl(function($row) {
            return config('sro.url').'dashboard/repair-orders/'.$row->id;
        })
        ->setTableRowUrlTarget(function($row) {
            return '_blank';
        });


        $this->setPerPageAccepted([25, 50, 100]);

        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

        $this->setDefaultSort('job_created_date', 'desc');

        $this->setFilterLayout('slide-down');

        $this->setFilterSlideDownDefaultStatusEnabled();


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

            Column::make('SRO Order #', 'sro_no')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('SX Repair Order #', 'sx_repair_order_no')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Brand', 'brand')
                ->searchable()
                ->hideIf(1),

            Column::make('Model', 'model')
                ->format(function ($value, $row) {
                    return ucwords(strtolower($row->brand)).' '.ucwords(strtolower($value));
                })
                ->excludeFromColumnSelect()
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Priority', 'priority')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Lane', 'lane')
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Need By', 'need_by_date')
                ->searchable()
                ->html(),

            Column::make('Warranty Vendor', 'warranty_vendor')
                ->html(),

            Column::make('Last Repair Date', 'last_repair_date')
                ->html(),

            Column::make('Transmission Number', 'transmission_no')
                ->html(),

            Column::make('Engine Model', 'engine_model')
                ->html(),

            Column::make('Status', 'status')
                ->html(),

            Column::make('Job Created At', 'job_created_date')
                ->sortable()
                ->format(function ($value, $row) {
                    return $value->toFormattedDateString();
                })
                ->excludeFromColumnSelect()
                ->html(),

        ];
    }

    public function filters(): array
    {
        return [

            SelectFilter::make('Eqp Type', 'type')
                ->options(RepairOrders::distinct('type')->where('customer_id', $this->customer->id)->pluck('type', 'type')->prepend('All', '')->toArray())
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('type', $value);
                    }
                }),
                SelectFilter::make('Priority')
                ->options(RepairOrders::distinct('priority')->where('customer_id', $this->customer->id)->pluck('priority', 'priority')->prepend('All', '')->toArray())
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('priority', $value);
                    }
                }),
                SelectFilter::make('Status')
                ->options(RepairOrders::distinct('status')->where('customer_id', $this->customer->id)->pluck('status', 'status')->prepend('All', '')->toArray())
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('status', $value);
                    }
                }),

        ];
    }

    public function builder(): Builder
    {
        return RepairOrders::where('customer_id', $this->customer->id);
    }
}
