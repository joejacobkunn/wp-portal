<?php

namespace App\Http\Livewire\Scheduler;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Zipcode;
use App\Models\Scheduler\Zones;
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
                'zip_code' => 'ZIP Codes',
                'zones' => 'Zones',
    ]]];
    protected $queryString = [
        'tabs.service-area-tabs.active' => ['except' => '', 'as' => 'tab'],
    ];
    protected $listeners = [
        'setDecription' => 'setDecription',
        'setBreadcrumb' => 'setBreadcrumb'
    ];
    public $breadcrumbs = [];
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

    public function setBreadcrumb($data)
    {
        $this->breadcrumbs = $data;
    }

    public function getZipBadgeCountProperty() {
        return Zipcode::where(['is_active' => 1, 'whse_id' => $this->activeWarehouse->id])->count();
    }

    public function getZoneBadgeCountProperty() {
        return Zones::where(['whse_id' => $this->activeWarehouse->id])->count();
    }
}
