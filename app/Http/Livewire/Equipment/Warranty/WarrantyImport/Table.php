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
            $this->setDefaultSort('warranty_imports.created_at', 'desc');
            $this->setPerPageAccepted([25, 50, 100]);
            $this->setTableAttributes([
                'class' => 'table table-bordered',
            ]);
        }

    public function columns(): array
    {
        return [
            Column::make('Total Records', 'total_records')
            ->hideIf(1)
            ->html(),

            Column::make('Name', 'name')
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Processed Records', 'processed_count')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row) {
                return $value.'/'.$row->total_records;
            })
            ->html(),

            Column::make('Uploaded At', 'created_at')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row) {
                return $value->diffForHumans();
            })
            ->html(),

            Column::make('Uploaded By', 'uploader.name')
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Status', 'status')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row) {
                $color = ($value == "queued") ? "warning" : "success";
                return '<span class="badge bg-light-'.$color.'">'.strtoupper($value).'</span>';
            })
            ->html(),

            Column::make('', 'id')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row) {
                $fileUrl = Storage::url($row->file_path);
                $failed = Storage::url($row->failed_records);
                return view('livewire.equipment.warranty.warranty-import.partials.action-dropdown',
                 [
                    'id' => $value,
                    'orginal'=>$fileUrl,
                    'failedPath'=>$failed,
                ])
                ->render();
            })
            ->html()
        ];
    }

    public function builder(): Builder
    {
        $query = WarrantyImports::query()
                    ->select([
                        'warranty_imports.name',
                        'file_path',
                        'uploaded_by',
                        'failed_records',
                        'processed_count',
                        'status',
                        'total_records'
                    ]);
        return $query;
    }
}
