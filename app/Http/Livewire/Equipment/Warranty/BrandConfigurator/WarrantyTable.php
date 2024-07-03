<?php
namespace App\Http\Livewire\Equipment\Warranty\BrandConfigurator;

use App\Http\Livewire\Component\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\Views\Column;

class WarrantyTable extends DataTableComponent
{
        public function configure(): void
        {
            $this->setPrimaryKey('id');
            $this->setDefaultSort('warranty_brand_configurations.created_at');
            $this->setPerPageAccepted([25, 50, 100]);
            $this->setTableAttributes([
                'class' => 'table table-bordered',
            ]);
        }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hideIf(1)
                ->html(),
            Column::make('Brand', 'brand.name')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return '<a  href="'.route('equipment.warranty.show', $row->id).
                        '" wire:navigate class="text-primary text-decoration-underline">'.
                        $value.'</a>';
                })
                ->html(),
            Column::make('Brand Prefix', 'prefix')
                ->excludeFromColumnSelect()
                ->searchable()
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->html(),
            Column::make('Alt Names', 'alt_name')
                ->excludeFromColumnSelect()
                ->searchable()
                ->format(function ($value, $row) {
                    return strtoupper($value);
                })
                ->html()
        ];
    }

    public function builder(): Builder
    {
        $query = BrandWarranty::with('brand')->where('account_id', Auth::user()->account_id);
        return $query;
    }

}
