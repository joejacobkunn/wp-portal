<?php

namespace App\Http\Livewire\Scheduler\ServiceArea;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Location;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Zipcode;
use App\Models\Scheduler\Zones;
use App\Traits\HasTabs;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use HasTabs;
    public $warehouses;
    public $activeWarehouse;
    public $whseId;
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
        'whseId' => ['except' => '', 'as' => 'whseId'],
    ];
    protected $listeners = [
        'setDecription' => 'setDecription',
        'setBreadcrumb' => 'setBreadcrumb'
    ];
    public $breadcrumbs = [];

    public function mount()
    {
        $this->warehouses = Warehouse::where('cono', 10)->orderBy('title')->get();

        $this->whseId =  $this->whseId ? $this->whseId : Auth::user()->office_location;

        $this->activeWarehouse = Warehouse::where('cono', 10)->where('id', $this->whseId)->first();
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.service-area.index');
    }

    public function setDecription($desc)
    {
        $this->Titledesc = $desc;
    }

    public function changeWarehouse($whseId)
    {
        $this->activeWarehouse = Warehouse::find($whseId);
        $this->whseId = $this->activeWarehouse->id;

    }

    public function setBreadcrumb($data)
    {
        $this->breadcrumbs = $data;
    }

    public function getZipBadgeCountProperty() {
        return Zipcode::where(['is_active' => 1, 'whse_id' => $this->activeWarehouse->id])->count();
    }

    public function getZoneBadgeCountProperty() {
        return Zones::where(['is_active' => 1, 'whse_id' => $this->activeWarehouse->id])->count();
    }

}
