<?php

namespace App\Http\Livewire\ReportingDashboard;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\ReportingDashboard\Traits\FormRequest;
use App\Models\Report\Dashboard;
use App\Models\Report\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use FormRequest,AuthorizesRequests;

    public $addDashboard = false;

    public $reports = [];

    public Dashboard $dashboard;

    public function breadcrumbs()
    {
        return [
            [
                'title' => 'Reporting Dashboard',
                'href' => route('reporting-dashboard.index'),
            ],
        ];
    }

    public function mount()
    {
        $this->authorize('viewAny', Dashboard::class);
        $this->formInit();
        $this->breadcrumbs = $this->breadcrumbs();

    }

    public function create()
    {
        $this->reports = Report::all();
        $this->addDashboard = true;
    }

    public function cancel()
    {
        $this->addDashboard = false;
    }




    public function render()
    {
        return $this->renderView('livewire.reporting-dashboard.index')->extends('livewire-app');
    }
}
