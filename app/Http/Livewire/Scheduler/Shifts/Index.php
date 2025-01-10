<?php

namespace App\Http\Livewire\Scheduler\Shifts;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
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
        'shift-tabs' => [
            'active' => 'ahm',
            'links' => [
                'ahm' => 'AHM',
                'delivery_pickup' => 'Delivery/Pickup',
    ]]];
    protected $queryString = [
        'tabs.shift-tabs.active' => ['except' => '', 'as' => 'tab'],
        'whseId' => ['except' => '', 'as' => 'whseId'],
    ];
    protected $listeners = [
        'setDecription' => 'setDecription',
        'setBreadcrumb' => 'setBreadcrumb'
    ];
    public $deliveryShift = [
        '8AM - 12PM',
        '12PM - 4PM',
        '4PM - 7PM',
        '9AM - 4PM',
    ];
    public $ahmShift = [
        '9AM - 1PM',
        '1PM - 6PM',
        '9AM - 4PM',
    ];

    public $breadcrumbs = [];

    public function mount()
    {
        $this->warehouses = Warehouse::where('cono', 10)->orderBy('title')->get();

        $this->whseId =  $this->whseId ? $this->whseId : Warehouse::where('title', Auth::user()->office_location)->first()->id;

        $this->activeWarehouse = Warehouse::where('cono', 10)->where('id', $this->whseId)->first();
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

    public function render()
    {
        return $this->renderView('livewire.scheduler.shifts.index');
    }
}
