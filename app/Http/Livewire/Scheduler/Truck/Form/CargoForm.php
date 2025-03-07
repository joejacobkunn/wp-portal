<?php
 namespace App\Http\Livewire\Scheduler\Truck\Form;

use App\Models\Core\User;
use App\Models\Scheduler\CargoConfigurator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CargoForm extends Form
{
    public ?CargoConfigurator $cargoConfigurator;

    public $product_category_id;
    public $height;
    public $length;
    public $width;
    public $weight;

    protected $validationAttributes = [
        'product_category_id' => 'Product Category',
        'height' => 'Height',
        'length' => 'Length',
        'width' => 'Width',
        'weight' => 'Weight',
    ];

    protected function rules()
    {
        return [
            'product_category_id' => [
                'required',
                'exists:product_category,id',
                Rule::unique('truck_cargo_configurator', 'product_category_id')->ignore($this->getConfigId()), // Ignore on update
            ],
            'height' => 'required',
            'length' => 'required',
            'width' => 'required',
            'weight' => 'required',
        ];
    }

    public function store($whse)
    {
        $validatedData = $this->validate();
        $validatedData['whse'] = $whse;
        CargoConfigurator::create($validatedData);
    }

    public function update()
    {
        $validatedData = $this->validate();
        $this->cargoConfigurator->fill($validatedData);
        $this->cargoConfigurator->save();
    }

    public function getConfigId()
    {
        return $this->cargoConfigurator?->id ?? null;
    }

    public function init($cargoConfigurator)
    {
        $this->cargoConfigurator = $cargoConfigurator;
        $this->fill($cargoConfigurator);
    }
}
