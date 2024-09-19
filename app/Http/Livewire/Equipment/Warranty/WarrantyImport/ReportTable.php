<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Equipment\Warranty\Report;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Equipment\Warranty\WarrantyImport\WarrantyImports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ReportTable extends DataTableComponent
{
        public function configure(): void
        {
            $this->setPrimaryKey('id');
            //$this->setDefaultSort('warranty_imports.created_at', 'desc');
            $this->setPerPageAccepted([25, 50, 100]);
            $this->setTableAttributes([
                'class' => 'table table-bordered',
            ]);
        }

    public function columns(): array
    {
        return [
            Column::make('Store', 'store')
            ->html(),

            Column::make('Customer Number', 'cust_no')
            ->hideIf(1)
            ->html(),

            Column::make('Description', 'description')
            ->hideIf(1)
            ->html(),


            Column::make('Customer Name', 'customer_name')
            ->format(function ($value, $row) {
                return $value.' ('.$row->cust_no.')';
            })
            ->html(),


            Column::make('Order Number', 'order_no')
            ->searchable()
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Brand', 'brand')
            ->searchable()
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Prod Line', 'prodline')
            ->searchable()
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Model', 'model')
            ->searchable()
            ->format(function ($value, $row) {
                return $value.' ('.$row->description.')';
            })
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Serial', 'serial')
            ->searchable()
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Sold On', 'sold')
            ->format(function ($value, $row) {
                return date("F j, Y", strtotime($value));
            })
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Reg Date', 'registration_date')
            ->searchable()
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Reg By', 'registered_by')
            ->searchable()
            ->excludeFromColumnSelect()
            ->html(),

        ];
    }

    public function builder(): Builder
    {
        $query = Report::query();
        return $query;
    }
}
