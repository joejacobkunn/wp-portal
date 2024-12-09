<?php

namespace App\Http\Livewire\ServiceArea\Zones;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\ServiceArea\Zones\Traits\FormRequest;
use App\Models\Core\Warehouse;

class Index extends Component
{
    use FormRequest;
    public $addRecord =false;
    public $warehouseId;

    public function create()
    {
        $this->addRecord= true;
        $this->dispatch('setDecription', 'Create New Zone');
    }

    public function mount()
    {
        $warehouse = Warehouse::find($this->warehouseId);
    }

    public function render()
    {
        return $this->renderView('livewire.service-area.zones.index');
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->reset(['name', 'description', 'days']);
    }

    public function submit()
    {
        $this->store($this->warehouseId);
    }

}
