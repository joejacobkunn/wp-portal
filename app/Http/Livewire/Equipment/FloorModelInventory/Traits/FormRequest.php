<?php
namespace App\Http\Livewire\Equipment\FloorModelInventory\Traits;

use App\Models\Equipment\FloorModelInventory\FloorModelInventory;
use App\Rules\ValidProductsForFloorModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait FormRequest
{
    use LivewireAlert;

    //attributes
    public $product;
    public $qty;
    public $warehouseId;

    protected $validationAttributes = [
        'warehouseId' => 'Warehouse',
        'qty' => 'Quantity',
        'product' => 'Product',
    ];

    protected function rules()
    {
        return [
            'warehouseId' => 'required|exists:warehouses,id',
            'qty' => 'required',
            'product' => [
                'required',
                'exists:products,prod',
                new ValidProductsForFloorModel,
                Rule::unique('floor_model_inventory')->where(function ($query) {
                    return $query->where('whse', $this->warehouseId);
                })->ignore($this->floorModel->id ?? null),
            ],
        ];
    }

    public function submit()
    {
        $this->validate();

        if (! empty($this->floorModel->id)) {
            $this->update();
            return;
        }
        $this->store();
    }

    public function store()
    {
        $this->authorize('store', FloorModelInventory::class);

        FloorModelInventory::create([
            'whse' =>$this->warehouseId,
            'product' =>$this->product,
            'qty' =>$this->qty,
            'sx_operator_id' =>Auth::user()->sx_operator_id
        ]);

        $this->alert('success','Inventory Record Created');
        return redirect()->route('equipment.floor-model-inventory.index');
    }
}
