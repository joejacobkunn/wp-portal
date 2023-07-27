<?php

namespace App\Http\Livewire\Core\User;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

    public User $user;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function boot(): void
    {

    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('core.user.show', $row->id).'" class="text-decoration-underline">'.$value.'</a>';
                })
                ->html(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('core.user.show', $row->id).'" class="text-decoration-underline">'.$value.'</a>';
                })
                ->html(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect(),

            BooleanColumn::make('Is Active', 'is_active')
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Active', 'is_active')
                ->options([
                    '' => 'All',
                    '0' => 'Active',
                    '1' => 'Deactivated',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('is_active', ($value == '1'));
                }),
        ];
    }

    public function builder(): Builder
    {
        return User::when(! auth()->user()->isMasterAdmin(), function ($query, $role) {
            $query->where('account_id', app('domain')->getClientId());
        });
    }
}
