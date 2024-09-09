<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Equipment\FloorModelInventory\Traits\FormRequest;
use App\Models\Core\Warehouse;
use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use App\Models\Product\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public $addRecord = false;
    public $warehouses;
    public $page;
    public $ShowUpdateModel;
    public FloorModelInventory $floorModel;

    public $breadcrumbs = [
        [
            'title' => 'Floor Model Inventory',
        ],
    ];

    protected $listeners = [
        'bulkUpdate' => 'bulkUpdate',
        'bulkDelete' => 'bulkDelete',
    ];

    public function mount()
    {
        $this->page ="Inventory List";
        $this->formInit();
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.index');
    }

    public function create()
    {
        $this->page ="Add New Inventory";
        $this->authorize('store', FloorModelInventory::class);
        $this->addRecord = true;
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->reset(['product', 'warehouseId', 'qty']);
    }

    public function bulkUpdate()
    {
    }

    public function bulkDelete($rows)
    {
        $this->ShowUpdateModel =true;
    }
}
