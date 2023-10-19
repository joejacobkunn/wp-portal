<?php

namespace App\Http\Livewire\ReportingDashboard;

use App\Http\Livewire\Component\Component;
use App\Models\Report\Dashboard;

class Broadcast extends Component
{
    public $dashboard;

    public $timestamp;

    public function mount(Dashboard $dashboard)
    {
        $this->dashboard = $dashboard;
        $this->timestamp = now();
    }

    public function render()
    {
        return $this->renderView('livewire.reporting-dashboard.broadcast')->extends('skeleton');
    }

    public function updateTableData()
    {
        $this->emitTo('reporting.reporting-table', 'refreshDatatable');
        $this->emitTo('reporting.second-reporting-table', 'refreshDatatable');
        $this->timestamp = now();
    }
}
