<?php

namespace App\Http\Livewire\SalesRepOverride;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\SalesRepOverride\Traits\SalesRepTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, SalesRepTrait;
    public $addRecord;

    public $breadcrumbs = [
        [
            'title' => 'Sales Rep Override',
        ],
    ];
    public function mount()
    {
    }

    public function create()
    {
        $this->addRecord = true;
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->reset(['customerNumber', 'shipTo', 'prodLine', 'salesRep']);

    }

    public function render()
    {
        return $this->renderView('livewire.sales-rep-override.index');

    }
}
