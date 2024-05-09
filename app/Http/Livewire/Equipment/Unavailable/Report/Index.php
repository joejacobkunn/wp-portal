<?php

namespace App\Http\Livewire\Equipment\Unavailable\Report;

use App\Models\Equipment\UnavailableReport;
use App\Http\Livewire\Component\Component;


class Index extends Component
{
    public $account;
    public $pending_report_count = 0;

    public $breadcrumbs = [
        [
            'title' => 'Reports',
        ],
    ];

    public function mount()
    {
        $this->account = account();
        $this->pending_report_count = $this->getPendingReportCount();
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.unavailable.report.index');
    }

    public function getConfiguredProperty()
    {
        if(!auth()->user()->can('equipment.unavailable.manage') && empty(auth()->user()->unavailable_equipments_id)) return false;
        return true;
    }

    private function getPendingReportCount()
    {
        return UnavailableReport::where('user_id', auth()->user()->id)->where('status', 'Pending Review')->count();
    }

}
