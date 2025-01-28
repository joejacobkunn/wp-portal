<?php
namespace App\Http\Livewire\Scheduler\ServiceArea\ZipCode;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Scheduler\Zipcode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Support\Str;

class Table extends DataTableComponent
{
    public $whseId;
    public function configure(): void
    {
        $this->setPrimaryKey('scheduler_zipcodes.id');
        $this->setDefaultSort('scheduler_zipcodes.created_at');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
            ->hideIf(1),
            Column::make('ZIP', 'zip_code')
            ->format(function ($value, $row)
            {
                return '<a  href="'.route('service-area.zipcode.show', $row->id).
                    '" wire:navigate class="text-primary text-decoration-underline">'.
                    $value.'</a>';
            })

            ->excludeFromColumnSelect()
            ->searchable()
            ->html(),

            Column::make('City', 'generalZipcode.city')
            ->excludeFromColumnSelect()
            ->searchable()
            ->html(),

            Column::make('State', 'generalZipcode.state')
            ->excludeFromColumnSelect()
            ->searchable()
            ->html(),

            Column::make('Zones', 'id')
            ->format(function ($value, $row){
                return $row->zones->implode('name', ', ');
            })
            ->excludeFromColumnSelect()
            ->searchable()
            ->html(),

            Column::make('Delivery Rate', 'delivery_rate')
            ->format(function ($value, $row){
                return '$'.number_format($value,2);
            })
            ->excludeFromColumnSelect()
            ->searchable()
            ->html(),

            Column::make('Pickup Rate', 'pickup_rate')
            ->format(function ($value, $row){
                return '$'.number_format($value,2);
            })
            ->excludeFromColumnSelect()
            ->searchable()
            ->html(),

            Column::make('Active', 'is_active')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row)
            {
                $class = $value ? 'success' : 'danger';
                $value = $value ? 'Active' : 'Inactive';
              return '<span class="badge bg-light-'.$class.'">'.$value.'</span>';
            })
            ->html(),

            ];
    }

    public function builder(): Builder
    {
        $query = Zipcode::with('generalZipcode')->where('scheduler_zipcodes.whse_id', $this->whseId);
        return $query;
    }
}
