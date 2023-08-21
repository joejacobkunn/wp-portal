<?php

namespace App\Http\Livewire\Core\Customer;

use App\Exports\CustomerEquipmentExport;
use App\Http\Livewire\Component\DataTableComponent;
use App\Models\SRO\Customer as SROCustomer;
use App\Models\SRO\Equipment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class EquipmentTable extends DataTableComponent
{
    use AuthorizesRequests;

    public SROCustomer $customer;

    public $type;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

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
                    return '<a target="_blank" href="'.config('sro.url').'dashboard/equipment/'.$row->id.'" class="link-underline-primary">'.ucwords(strtolower($value)).' '.$row->model.'</a>';
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

            Column::make('7YEPP', 'sx_equipment_order_no')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    $yepp_status = $this->SevenYeppStatus($row->model, $row->serial_no);
                    if($yepp_status['status'] == 'Inactive')
                        return sprintf('<ul><a href="javascript:void(0)"><span wire:click="$emitUp(\'fetchServicePlans\',\'%s\',\'%s\')" class="badge bg-light-secondary"><abbr title="Click to view service logs">Inactive</abbr></span></a></ul>',$row->model,$row->serial_no);
                    else
                        return sprintf('<ul><a href="javascript:void(0)"><span wire:click="$emitUp(\'fetchServicePlans\',\'%s\',\'%s\')" class="badge bg-light-success"><abbr title="Click to view service logs">Active: %s Year (Last Serv %s)</abbr></span></a></ul>',$row->model,$row->serial_no,ordinal(intval($yepp_status['year'])),date('m-d-Y',strtotime($yepp_status['last_service'])));

                })
                ->hideIf(strtolower($this->type) != 'hom')
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

    public function fetchServicePlans($model_number, $serial_number)
    {
        dd($model_number, $serial_number);
    }

    private function SevenYeppStatus($model_number, $serial_number)
    {
        $status = DB::connection('sx')->select("SELECT
                                                    CASE
                                                    WHEN s.user5 = 'Y' THEN 'Active'
                                                    ELSE 'Inactive'
                                                    END AS 'YEPP_Status',
                                                    s.user6 AS 'YEPP_Year',
                                                    s.user8 AS 'YEPP_LastService'
                                                    FROM pub.icses s
                                                    WHERE s.cono = 10
                                                    AND s.prod = '".$model_number."'
                                                    AND s.serialno = '".$serial_number."'
                                                    AND s.custno <> 0
                                                WITH(NOLOCK)");

        if(is_null($status) || empty($status)) return ['status' => 'Inactive'];


        return ['status' => $status[0]->YEPP_Status, 'year' => $status[0]->YEPP_Year, 'last_service' => $status[0]->YEPP_LastService];
    }
}
