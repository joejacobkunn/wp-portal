<?php

namespace App\Http\Livewire\Core\Customer;

use App\Exports\CustomerEquipmentExport;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\SRO\Customer as SROCustomer;
use App\Models\SRO\Equipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class EquipmentTable extends DataTableComponent
{
    use AuthorizesRequests;

    public SROCustomer $customer;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableRowUrl(function ($row) {
                return config('sro.url').'dashboard/equipment/'.$row->id;
            })
            ->setTableRowUrlTarget(function ($row) {
                return '_blank';
            });

        $this->setPerPageAccepted([25, 50, 100]);

        $this->setFilterLayout('slide-down');

        $this->setFilterSlideDownDefaultStatusEnabled();

        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);

        $this->setDefaultSort('purchase_date', 'desc');

    }

    public function boot(): void
    {
    }

    public function bulkActions(): array
    {
        return [
            'exportToExcel' => 'Export to Excel',
        ];
    }

    public function columns(): array
    {
        return [

            Column::make('Id', 'id')
                ->excludeFromColumnSelect()
                ->hideIf(1)
                ->html(),

            Column::make('Name', 'brand')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return ucwords(strtolower($value)).' '.ucwords(strtolower($row->model));
                })
                ->html(),

            Column::make('Brand', 'brand')
                ->searchable()
                ->hideIf(1),

            Column::make('Model', 'model')
                ->searchable()
                ->hideIf(1),

            Column::make('Type', 'type')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Serial Number', 'serial_no')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('SX Order #', 'sx_equipment_order_no')
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Sales Rep', 'sales_rep')
                ->searchable()
                ->html(),

            Column::make('Warranty Vendor', 'warranty_vendor')
                ->html(),

            Column::make('Last Repair', 'last_repair_date')
                ->html(),

            Column::make('Transmission Number', 'transmission_no')
                ->html(),

            Column::make('Engine Model', 'engine_model')
                ->html(),

            Column::make('Engine Serial', 'engine_serial_no')
                ->html(),

            Column::make('Purchase Date', 'purchase_date')
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

            SelectFilter::make('Type')
                ->options(Equipment::distinct('type')->where('customer_id', $this->customer->id)->pluck('type', 'type')->prepend('All', '')->toArray())
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('type', $value);
                    }
                }),

            SelectFilter::make('Brand')
                ->options(Equipment::distinct('brand')->where('customer_id', $this->customer->id)->pluck('brand', 'brand')->prepend('All', '')->toArray())
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->where('brand', $value);
                    }
                }),

            SelectFilter::make('Purchase Date Year')
                ->options(['' => 'All Years'] + array_combine(range(date('Y'), now()->subYear(11)->format('Y')), range(date('Y'), now()->subYear(11)->format('Y'))))
                ->filter(function (Builder $builder, string $value) {
                    if ($value) {
                        $builder->whereYear('purchase_date', $value);
                    }
                }),

        ];
    }

    public function builder(): Builder
    {
        return Equipment::where('customer_id', $this->customer->id);
    }

    public function exportToExcel()
    {
        return Excel::download(new CustomerEquipmentExport($this->customer, $this->getSelected()), 'equipments_user_'.$this->customer->sx_customer_id.'_export.xlsx');
    }
}
