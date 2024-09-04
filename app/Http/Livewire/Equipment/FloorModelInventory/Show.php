<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Equipment\FloorModelInventory\Traits\FormRequest;
use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use App\Models\SX\Product;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Show extends Component
{
    use AuthorizesRequests, HasTabs, FormRequest;

    public FloorModelInventory $floorModel;
    public $warehouses;
    public $page;
    public $qtyModal = false;
    public $sxproduct;
    public $enableButton = false;

    public $tabs = [
        'floor-model-comment-tabs' => [
            'active' => 'comments',
            'links' => [
                'comments' => 'Comments',
                'activity' => 'Activity',
            ],
        ],
    ];

    public $breadcrumbs = [[
        'title' => 'Floor Model Inventory',
        'href' => 'javascript:window.history.back()',
    ]];

    public $actionButtons = [
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
        'closeQtyUpdate' => 'closeQtyUpdate',
        'qty:changed' => 'updateQuantity'
    ];

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.show');
    }

    public function mount()
    {
        $this->authorize('view', $this->floorModel);
        $this->page = 'View Floor Model Inventory';
        $this->breadcrumbs = array_merge($this->breadcrumbs,[
            ['title' => $this->floorModel->warehouse->title],
            ['title' => $this->floorModel->product]
        ]);
        $this->formInit();

        $this->sxproduct = $this->getProduct();
    }

    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['product', 'warehouseId', 'qty']);
        $this->formInit();
        $this->updatedProduct();
    }

    public function showModel()
    {
        $this->qtyModal = true;
    }

    public function closeQtyUpdate()
    {
        $this->qtyModal = false;
    }

    private function getProduct()
    {
        if(config('sx.mock')) return [];

        $product = Product::where('cono', 10)->where('prod', $this->floorModel->product)->first();
        return $product->getMetaData()[0];
    }

    public function updateQuantity($cartIndex, $value, $recheckValidation = true)
    {
        if($this->floorModel->qty != $value) $this->enableButton = true;
        else $this->enableButton = false;

        $this->qty = $value;
    }

}
