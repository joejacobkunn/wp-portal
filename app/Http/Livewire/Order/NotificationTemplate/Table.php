<?php

namespace App\Http\Livewire\Order\NotificationTemplate;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Order\NotificationTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\BooleanColumn;

class Table extends DataTableComponent
{
    use AuthorizesRequests;

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
        //$this->authorize('viewAny', NotificationTemplate::class);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()->searchable()->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return '<a href="'.route('order.email-template.show', ['template' => $row->id]).'" wire:navigate class="text-primary text-decoration-underline">'.$value.'</a>';
                })
                ->html(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->format(function ($value, $row) {
                    return '<a href="'.route('order.email-template.show', ['template' => $row->id]).'" wire:navigate class="text-primary text-decoration-underline">'.$value.'</a>';
                })
                ->excludeFromColumnSelect()
                ->html(),

                Column::make('Type', 'type')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {
                    return $value;
                })
                ->html(),

            BooleanColumn::make('Active', 'is_active')->sortable(),
        ];
    }

    public function builder(): Builder
    {
        return NotificationTemplate::where('account_id', account()->id);
    }
}
