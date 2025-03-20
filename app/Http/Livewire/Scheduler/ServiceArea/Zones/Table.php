<?php
namespace App\Http\Livewire\Scheduler\ServiceArea\Zones;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Scheduler\Zones;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Support\Str;

class Table extends DataTableComponent
{
    public $whseId;
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('zones.created_at');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
        $this->setSearchDebounce(350);
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hideIf(1),
            Column::make('Zone', 'name')
                ->searchable()
                ->sortable()->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return '<a  href="'.route('service-area.zones.show', $row->id).
                        '" wire:navigate class="text-primary text-decoration-underline">'.
                        $value.'</a>';
                })
                ->html(),
            Column::make('Service', 'service')
                ->sortable()->excludeFromColumnSelect()
                // search of zipcode also added here since we dont have zipcode column
                ->searchable(function (Builder $query, $search) {
                    $isZipcode = is_numeric($search);

                    $query->where('zones.service', 'like', '%' . $search . '%');

                    if ($isZipcode) {
                        $query->orWhereHas('zipcodes', function ($q) use ($search) {
                            $q->where('scheduler_zipcodes.zip_code', 'like', '%' . $search . '%');
                        });
                    }
                })
                ->format(function ($value, $row)
                {
                    return $value->label();
                })
                ->html(),

            Column::make('Description', 'description')
                ->excludeFromColumnSelect()
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
        return Zones::where('whse_id', $this->whseId)
        ->with('zipcodes');

    }
}
