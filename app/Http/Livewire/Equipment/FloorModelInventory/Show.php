<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Equipment\FloorModelInventory\Traits\FormRequest;
use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Show extends Component
{
    use AuthorizesRequests, FormRequest;

    public FloorModelInventory $floorModel;
    public $editRecord =false;
    public $warehouses;
    public $breadcrumbs = [[
            'title' => 'Floor Model Inventory',
            'route_name' => 'equipment.floor-model-inventory.index',
        ]];
    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => 'Confirm Delete',
            'listener' => 'deleteRecord',
        ],
    ];

    protected $listeners = [
        'deleteRecord' => 'delete',
        'edit' => 'edit',
    ];

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.show');
    }

    public function mount()
    {
        $this->authorize('view', $this->floorModel);
        $this->breadcrumbs = array_merge($this->breadcrumbs,[
            ['title' => $this->floorModel->warehouse->title],
            ['title' => $this->floorModel->product]
        ]);
        $this->formInit();
    }

    public function edit()
    {
        $this->authorize('update', $this->floorModel);
        $this->editRecord = true;
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['product', 'warehouseId', 'qty']);
        $this->formInit();
        $this->updatedProduct();
    }
}
