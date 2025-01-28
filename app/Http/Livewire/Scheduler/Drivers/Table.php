<?php

namespace App\Http\Livewire\Scheduler\Drivers;


use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\User;
use App\Models\Scheduler\Zones;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    public $whseId;
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('users.created_at');
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
            Column::make('Name', 'name')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return '<a  href="'.route('schedule.driver.show', $row->id).
                        '" wire:navigate class="text-primary text-decoration-underline">'.
                        $value.'</a>';
                })
                ->html(),

            Column::make('Email', 'email')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),
            Column::make('Title', 'title')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),

            Column::make('Office Location', 'office_location')
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
        return User::query()->whereIn('title', ['Driver', 'Service Technician']);
    }
}
