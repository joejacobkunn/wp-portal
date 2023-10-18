<?php

namespace App\Http\Livewire\Reporting;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Reporting\Traits\FormRequest;
use App\Models\Report\Report;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Index extends Component
{
    use FormRequest,AuthorizesRequests;

    public $addReport = false;

    public Report $report;

    public $group_by_options = [];

    public function breadcrumbs()
    {
        return [
            [
                'title' => 'Reporting',
                'href' => route('reporting.index'),
            ],
        ];
    }

    public function mount()
    {
        //$this->authorize('viewAny', Report::class);
        $this->formInit();
        $this->breadcrumbs = $this->breadcrumbs();

    }


    public function render()
    {
        return $this->renderView('livewire.reporting.index')->extends('livewire-app');
    }

    public function createReport()
    {
        $this->authorize('store', Report::class);
        $this->addReport = true;
    }


    public function cancel()
    {
        $this->addReport = false;
    }

}
