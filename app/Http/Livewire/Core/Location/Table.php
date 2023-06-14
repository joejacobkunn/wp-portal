<?php

namespace App\Http\Livewire\Core\Location;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Core\Account;
use App\Models\Core\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

    public Account $account;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');

        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function boot(): void
    {
        $this->authorize('viewAny', Location::class);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    //return '<a href="'.route('core.account.show', $row->id).'" class="text-primary text-decoration-underline">' . $value . '</a>';
                    return $value;
                })
                ->html(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    //return '<a href="'.route('core.account.show', $row->id).'" class="text-primary text-decoration-underline">' . $value . '</a>';
                    return $value;
                })
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Phone', 'phone')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->html(),

            Column::make('Active', 'is_active')
                ->sortable()
                ->searchable()
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return $value ? '<span style="color:green"><i class="far fa-check-circle"></i></span>' : '<span style="color:red"><i class="far fa-times-circle"></i></span>';
                })
                ->html(),

            Column::make('Created At', 'created_at')
                ->sortable()->searchable()->deselected()
                ->format(function ($value) {
                        if ($value) {
                            return $value->format(config('app.default_datetime_format'));
                        }
                    }),

        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function builder(): Builder
    {
        return Location::where('account_id', $this->account->id);
    }
}
