<?php

namespace App\Http\Livewire\Scheduler;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
use App\Traits\HasTabs;

class Index extends Component
{
    use HasTabs;
    public $warehouses;
    public $activeWarehouse;
    public $Titledesc;
    public $tabs = [
        'service-area-tabs' => [
            'active' => 'zones',
            'links' => [
                'zip_code' => 'Zip Codes',
                'zones' => 'Zones',
    ]]];
    protected $queryString = [
        'tabs.service-area-tabs.active' => ['except' => '', 'as' => 'tab'],
    ];
    protected $listeners = [
        'setDecription' => 'setDecription'
    ];

    public function mount()
    {
        $this->warehouses = Warehouse::where('cono', 10)->orderBy('title')->get();
        $this->activeWarehouse = $this->warehouses->first();
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.index');
    }

    public function setDecription($desc)
    {
        $this->Titledesc = $desc;
    }

    public function changeWarehouse($whseId)
    {
        $this->activeWarehouse = Warehouse::find($whseId);
    }
}
