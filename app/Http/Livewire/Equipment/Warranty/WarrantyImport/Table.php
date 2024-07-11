<?php
namespace App\Http\Livewire\Equipment\Warranty\WarrantyImport;

use App\Http\Livewire\Component\DataTableComponent;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Equipment\Warranty\WarrantyImport\WarrantyImports;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
        public function configure(): void
        {
            $this->setPrimaryKey('id');
            $this->setDefaultSort('warranty_imports.created_at');
            $this->setPerPageAccepted([25, 50, 100]);
            $this->setTableAttributes([
                'class' => 'table table-bordered',
            ]);
        }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hideIf(1)
                ->html(),
            Column::make('File Name', 'file_path')
                ->excludeFromColumnSelect()
                ->searchable()
                ->format(function ($value, $row) {

                    $fileUrl = Storage::url($value);

                    return '<a href="'.$fileUrl.'" class="text-primary text-decoration-underline">'.$value.'</a>';
                })
                ->html(),
            Column::make('Processed Records', 'processed_count')
                ->excludeFromColumnSelect()
                ->html(),
            Column::make('failed record', 'failed_records')
                ->excludeFromColumnSelect()
                ->format(function ($value, $row) {

                    $fileUrl = Storage::url($value);

                    return '<a href="'.$fileUrl.'" class="text-primary text-decoration-underline">'.$value.'</a>';
                })
                ->html(),
            Column::make('Created At', 'created_at')
            ->sortable(),
        ];
    }

    public function builder(): Builder
    {
        $query = WarrantyImports::query();
        return $query;
    }
}
