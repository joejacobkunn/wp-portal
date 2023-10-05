<?php

namespace App\Http\Livewire\Core\Role;

use App\Models\Core\Role;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Table extends DataTableComponent
{
    use AuthorizesRequests;
    
    public Role $role;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'asc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function boot(): void
    {
        $this->authorize('viewAny', Role::class);
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->hideIf(true),

            Column::make("Name", "label")
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('core.role.show', $row).'" class="text-primary text-decoration-underline">' . $value . '</a>';
                })
                ->html(),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function builder(): Builder
    {
        $roleQuery = Role::query();

        if (! auth()->user()->isMasterAdmin()) {
            $roleQuery->where('account_id', app('domain')->getClientId());
        } else {
            $roleQuery->whereNull('account_id');
        }
        
        return $roleQuery;
    }
}
