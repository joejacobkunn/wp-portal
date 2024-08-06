<?php

namespace App\Http\Livewire\Marketing\SMSMarketing;

use App\Http\Livewire\Component\DataTableComponent;
use App\Models\Marketing\SMSMarketing;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Rappasoft\LaravelLivewireTables\Views\Column;

class Table extends DataTableComponent
{
    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('created_at', 'desc');
        $this->setPerPageAccepted([25, 50, 100]);
        $this->setTableAttributes([
            'class' => 'table table-bordered',
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Total Records', 'total_count')
            ->hideIf(1)
            ->html(),

            Column::make('Name', 'name')
            ->excludeFromColumnSelect()
            ->html(),

            Column::make('Processed Records', 'processed_count')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row) {
                $text = $value.'/'.$row->total_count;

                if($value != $row->total_count) return $text.'<span class="badge bg-light-warning float-end"><i class="fas fa-exclamation-triangle"></i> '.($row->total_count - $value).'</span>';
                return $text.'<span class="badge bg-light-success float-end"><i class="far fa-check-circle"></i></span>';
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
                $color = ($value == "queued" || $value == "processing") ? "warning" : "success";
                return '<span class="badge bg-light-'.$color.'">'.strtoupper($value).'</span>';
            })
            ->html(),

            Column::make('', 'id')
            ->excludeFromColumnSelect()
            ->format(function ($value, $row) {
                $fileUrl = Storage::url($row->file);
                $failed = $row->failed_file ? Storage::url($row->failed_file): null;
                return view('livewire.marketing.sms-marketing.partials.action-dropdown',
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
        $query = SMSMarketing::query()
        ->select([
            'smsmarketing.name',
            'file',
            'created_by',
            'failed_file',
            'processed_count',
            'status',
            'total_count'
        ]);
        return $query;
    }
}
