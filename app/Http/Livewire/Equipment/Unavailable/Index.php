<?php

namespace App\Http\Livewire\Equipment\Unavailable;

use App\Http\Livewire\Component\Component;
use App\Models\Equipment\UnavailableReport;

class Index extends Component
{
    public $account;
    public $pending_report_count = 0;

    public $breadcrumbs = [
        [
            'title' => 'Demo Equipments',
        ],
    ];

    public function mount()
    {
        $this->account = account();
        $this->pending_report_count = $this->getPendingReportCount();
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.unavailable.index');
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
