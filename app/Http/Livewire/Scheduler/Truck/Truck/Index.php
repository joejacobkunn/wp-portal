<?php

namespace App\Http\Livewire\Scheduler\Truck\Truck;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Truck\Traits\FormRequest;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Truck;

class Index extends Component
{
    public $addRecord = false;
    public Truck $truck;
    public $warehouseId;
    public $activeWarehouseId;
    public $activeWarehouse;
    use AuthorizesRequests, FormRequest;

    public function getActiveWarehouseProperty()
    {
        $data = Warehouse::select(['id', 'short', 'title'])
        ->where('id', $this->activeWarehouseId)
        ->first();

        return $data;
    }

    public function mount()
    {
        // $this->authorize('view', Truck::class);
        $this->formInit();
        $this->setActiveWarehouse($this->warehouseId);
        $this->activeWarehouse =Warehouse::select(['id', 'short', 'title'])
            ->where('id', $this->activeWarehouseId)
            ->first();
    }


    public function setActiveWarehouse($warehouseId)
    {
        $this->activeWarehouseId = $warehouseId;
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.truck.index')->extends('livewire-app');
    }

    public function create()
    {
        $this->addRecord =true;
    }

    public function cancel()
    {
        $this->addRecord = false;
    }
}
