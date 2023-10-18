<?php

namespace App\Http\Livewire\ReportingDashboard;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\ReportingDashboard\Traits\FormRequest;
use App\Models\Report\Dashboard;
use App\Models\Report\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class Show extends Component
{
    use AuthorizesRequests, FormRequest;

    public Dashboard $dashboard;

    public $actionButtons = [];

    public $editRecord = false;

    public $reports = [];

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
                'title' => 'Reporting Dashboards',
                'href' => route('reporting-dashboard.index'),
            ],
            [
                'title' => $this->dashboard->name,
            ],
        ];
    }

    public function mount()
    {
        //$this->authorize('view', $report);
        $this->formInit();
        $this->breadcrumbs = $this->breadcrumbs();


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
        return $this->renderView('livewire.reporting-dashboard.show');
    }

    public function edit()
    {
        //$this->authorize('update', $this->report);
        $this->reports = Report::all();

        $this->editRecord = true;
    }

    /**
     * Delete existing user
     */
    public function delete()
    {
        //$this->authorize('delete', $this->report);

        $this->dashboard->delete();

        session()->flash('success', 'Dashboard deleted !');

        return redirect()->route('reporting-dashboard.index');
    }

    public function cancel()
    {
        $this->editRecord = false;
    }

}
