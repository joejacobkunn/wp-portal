<?php
namespace App\Http\Livewire\ServiceArea\Zones;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\ServiceArea\Zones;
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
                ->html(),
            Column::make('Description', 'description')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),
            Column::make('Scheduled Days', 'schedule_days')
                ->excludeFromColumnSelect()
                ->searchable()
                ->format(function ($value, $row)
                {

                    $enabledDays = array_filter($value, function ($day) {
                        return $day['enabled'] === true;
                    });
                    $outString = '<span>';
                    foreach($enabledDays as $key =>  $day) {
                        $title = Str::title(str_replace(['-', '_'], ' ', $day['schedule']));
                        $outString .= $key.' : ' .$title.'<br>';

                    }

                    return $outString. '</span>';
                })
                ->html(),

            ];
    }

    public function builder(): Builder
    {
        $query = Zones::where('whse_id', $this->whseId);
        return $query;
    }
}
