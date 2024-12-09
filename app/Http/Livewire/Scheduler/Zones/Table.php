<?php
namespace App\Http\Livewire\Scheduler\Zones;

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
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->html(),
            Column::make('Zone', 'name')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return '<a  href="'.route('service-area.zones.show', $row->id).
                        '" wire:navigate class="text-primary text-decoration-underline">'.
                        $value.'</a>';
                })
                ->html(),
            Column::make('Description', 'description')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),


            ];
    }

    public function builder(): Builder
    {
        $query = Zones::where('whse_id', $this->whseId);
        return $query;
    }
}
