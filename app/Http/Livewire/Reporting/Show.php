<?php

namespace App\Http\Livewire\Reporting;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Reporting\Traits\FormRequest;
use App\Models\Report\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Show extends Component
{
    use AuthorizesRequests, FormRequest;

    public $editRecord = false;

    public $actionButtons = [];

    public Report $report;

    public $report_data = [];

    protected $listeners = [
        'deleteRecord' => 'delete',
        'edit' => 'edit',
        'updateStatus' => 'updateStatus',
        'closeModal' => 'closeModal'
    ];

    public function breadcrumbs()
    {
        return [
            [
                'title' => 'Reporting',
                'href' => route('reporting.index'),
            ],
            [
                'title' => $this->report->name,
            ],
        ];
    }

    public function mount(Report $report)
    {
        //$this->authorize('view', $report);
        $this->formInit();
        $this->breadcrumbs = $this->breadcrumbs();
        $this->report_data = DB::connection('sx')->select($this->report->query);


        $this->actionButtons = [
            [
                'icon' => 'fa-edit',
                'color' => 'primary',
                'listener' => 'edit',
            ],
            [
                'icon' => 'fa-trash',
                'color' => 'danger',
                'confirm' => true,
                'confirm_header' => 'Confirm Delete',
                'listener' => 'deleteRecord',
            ],
        ];
    }

    public function render()
    {
        return $this->renderView('livewire.reporting.show');
    }

    public function loadReport()
    {
        $this->report_data = DB::connection('sx')->select($this->report->query);
    }

    // @TODO Remove after confirmation on WP-8 Remove User info Edit
    public function edit()
    {
        $this->authorize('update', $this->report);
        $this->editRecord = true;
    }

    /**
     * Delete existing user
     */
    public function delete()
    {
        $this->authorize('delete', $this->report);

        $this->report->delete();

        session()->flash('success', 'Report deleted !');

        return redirect()->route('reporting.index');
    }

    public function cancel()
    {
        $this->editRecord = false;
    }
}
