<?php

namespace App\Http\Livewire\ServiceArea;

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
                'zones' => 'Zones',
                'zip-codes' => 'Zip Codes',
    ]]];
    protected $queryString = [
        'tabs.service-area-tabs.active' => ['except' => '', 'as' => 'tab'],
    ];
    protected $listeners = [
        'setDecription' => 'setDecription'
    ];

    public function mount()
    {
        $this->warehouses = Warehouse::where('cono', 10)->get();
        $this->activeWarehouse = $this->warehouses[0]->id;
    }

    public function render()
    {
        return $this->renderView('livewire.service-area.index');
    }

    public function setDecription($desc)
    {
        $this->Titledesc = $desc;
    }

    public function changeWarehouse($whseId)
    {
        $warehouse = Warehouse::select('id', 'title')->find($whseId);
        $this->activeWarehouse = $warehouse?->id;
        $this->setDecription('list of Zones in : '.$warehouse?->title);
    }
}
