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
    public $ShowLoader =false;
    public FloorModelInventory $floorModel;
    public $productSearchModal = false;
    public $breadcrumbs = [
        [
            'title' => 'Floor Model Inventory',
        ],
    ];
    protected $listeners = [
        'closeProductSearch'=>'closeProductSearch',
        'addProductsToForm'=>'addProductsToForm'
    ];

    public function mount()
    {
        $this->warehouses = Warehouse::where('cono',10)->select('id','title')->get();
        $this->warehouseId = Auth::user()->office_location;
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.index');
    }

    public function closeProductSearch()
    {
        $this->productSearchModal =false;
    }

    public function SelectProduct()
    {
        $this->productSearchModal =true;
    }

    public function addProduct($item)
    {
        $this->product = $item;
        $this->resetValidation('product');

    }

    public function addProductsToForm($product)
    {
        $this->product = $product;
        $this->closeProductSearch();
    }

    public function create()
    {
        //$this->authorize('view', FloorModelInventory::class);
        $this->addRecord = true;
    }

    public function cancel()
    {
        $this->addRecord = false;
    }
}
