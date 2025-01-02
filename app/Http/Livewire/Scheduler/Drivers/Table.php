<?php

namespace App\Http\Livewire\Scheduler\Drivers;


use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\User;
use App\Models\Scheduler\StaffInfo;
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
        $this->setDefaultSort('scheduler_staff_info.created_at');
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
            Column::make('User ID', 'user_id')
            ->hideIf(1),
            Column::make('Name', 'user.name')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row)
                {
                    return '<a  href="'.route('core.user.show', $row->user_id).
                        '" wire:navigate class="text-primary text-decoration-underline">'.
                        $value.'</a>';
                })
                ->html(),

            Column::make('Description', 'description')
                ->excludeFromColumnSelect()
                ->html(),
            Column::make('Email', 'user.email')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),
            Column::make('Title', 'user.title')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),

            Column::make('Office Location', 'user.office_location')
                ->excludeFromColumnSelect()
                ->searchable()
                ->html(),

            Column::make('Active', 'user.is_active')
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
        return StaffInfo::query()->with('user')
        ->whereHas('user', function ($query) {
            $query->whereIn('title', ['Driver', 'Service Technician']);
        });
    }
}
