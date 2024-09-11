<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory;

use App\Events\Floormodel\InventoryDeleted;
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
    public $selectedRows;
    public $ShowDeleteModel;

    public FloorModelInventory $floorModel;

    public $breadcrumbs = [
        [
            'title' => 'Floor Model Inventory',
        ],
    ];

    protected $listeners = [
        'bulkUpdate' => 'bulkUpdateListner',
        'bulkDelete' => 'bulkDeleteListner',
        'closeUpdate' => 'closeUpdate',
        'closeDelete' => 'closeDelete',
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
        $this->ShowUpdateModel =false;
        $this->resetValidation();
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->reset(['product', 'warehouseId', 'qty']);
    }

    public function bulkDeleteListner($rows)
    {
        $this->selectedRows =$rows;
        $this->getSelectedRecords();
        $this->ShowDeleteModel = true;
    }

    public function bulkDelete()
    {
        $floorModel = FloorModelInventory::latest()->first();

        $this->authorize('delete', $floorModel);

        $floor_models = FloorModelInventory::whereIn('id', $this->selectedRows)->get();

        foreach($floor_models as $floor_model)
        {
            InventoryDeleted::dispatch($floor_model);
            $floor_model->delete();
        }

        $this->reset('selectedRows');
        $this->ShowDeleteModel = false;
        $this->tableKey = uniqid();
        $this->alert('success', 'Bulk Delete Completed');
    }

    public function bulkUpdateListner($rows)
    {
        $this->selectedRows =$rows;
        $this->getSelectedRecords();
        $this->ShowUpdateModel =true;
    }

    public function bulkUpdate()
    {
        $this->bulkQtyUpdate();
        $this->alert('success', 'Bulk update completed !');
        $this->closeUpdate();
    }

    public function closeUpdate()
    {
        $this->ShowUpdateModel =false;
        $this->reset(['bulkqty', 'comments']);
        $this->resetValidation();
    }

    public function closeDelete()
    {
        $this->ShowDeleteModel =false;
        $this->resetValidation();
    }
}
