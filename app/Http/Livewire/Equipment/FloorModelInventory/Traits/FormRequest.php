<?php
namespace App\Http\Livewire\Equipment\FloorModelInventory\Traits;

use App\Events\Floormodel\InventoryAdded;
use App\Events\Floormodel\InventoryDeleted;
use App\Events\Floormodel\InventoryUpdated;
use App\Models\Core\Warehouse;
use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use App\Models\Product\Product;
use App\Models\SX\Product as SXProduct;
use App\Rules\ValidProductsForFloorModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait FormRequest
{
    use LivewireAlert;

    //attributes
    public $product;
    public $qty =0;
    public $warehouseId;

    public $showBox =false;
    public $matchedProduct;
    protected $validationAttributes = [
        'warehouseId' => 'Warehouse',
        'qty' => 'Quantity',
        'product' => 'Product',
    ];

    protected function rules()
    {
        return [
            'warehouseId' => 'required|exists:warehouses,id',
            'qty' => 'required|integer|in:0,1,2,3',
            'product' => [
                'required',
                //'exists:products,prod',
                new ValidProductsForFloorModel,
                Rule::unique('floor_model_inventory')->where(function ($query) {
                    return $query->where('whse', $this->warehouseId);
                })->ignore($this->floorModel->id ?? null),
            ],
        ];
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        $this->warehouses = Warehouse::where('cono',10)->select('id','title')->get();
        $this->warehouseId = Warehouse::where('cono',10)->where('title', Auth::user()->office_location)->first()?->id;
        if (!empty($this->floorModel->id)) {
            $this->product = $this->floorModel->product;
            $this->warehouseId = $this->floorModel->whse;
            $this->qty = $this->floorModel->qty;
        }
    }

    public function updatedProduct()
    {
        $this->showBox =false;
        $this->validateOnly('product');
        $this->matchedProduct = $this->getMatchedProduct();
        $this->showBox =true;
    }

    private function getMatchedProduct()
    {
        if(config('sx.mock'))
        {
            return Product::where('prod',$this->product)->first();
        }else{
            $product = SXProduct::where('cono', 10)->where('prod', $this->product)->first();
            return $product->getMetaData()[0];
        }
    }

    public function submit()
    {

        if (! empty($this->floorModel->id)) {
            $this->update();
            return;
        }
        $this->validate();
        $this->store();
    }

    public function store()
    {
        $this->authorize('store', FloorModelInventory::class);

        $inventory = FloorModelInventory::create([
            'whse' => $this->warehouseId,
            'product' => strtoupper($this->product),
            'qty' => $this->qty,
            'sx_operator_id' => Auth::user()->sx_operator_id
        ]);

        InventoryAdded::dispatch($inventory);

        $this->alert('success','Inventory Record Created');
        return redirect()->route('equipment.floor-model-inventory.index');
    }

    /**
     * Update existing record
     */
    public function update()
    {

        $this->authorize('update', $this->floorModel);

        $this->validate([
            'qty' => 'required|integer|in:0,1,2,3'
        ]);

        $this->floorModel->fill([
            'qty' => $this->qty ?? 0
        ]);
        $this->floorModel->save();

        InventoryUpdated::dispatch($this->floorModel);

        $this->qtyModal = false;

        $this->alert('success', 'Record updated!');
    }

    public function delete()
    {
        $this->authorize('delete', $this->floorModel);

        InventoryDeleted::dispatch($this->floorModel);

        if ( FloorModelInventory::where('id', $this->floorModel->id )->delete() ) {
            $this->alert('success', 'Record deleted!');
            return redirect()->route('equipment.floor-model-inventory.index');
        }


        $this->alert('error','Record not found');
        return redirect()->route('equipment.floor-model-inventory.index');
    }
}
