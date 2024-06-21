<?php
namespace App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Traits;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Product\Brand;
use App\Models\Product\Line;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait FormRequest
{
    use LivewireAlert;
    public $brands =[];
    public $lines = [];

    //attributes
    public $brandId;
    public $lineId;
    public $registrationUrl;
    public $requireProof;

    protected $validationAttributes = [
        'brandId' => 'Brand',
        'lineId' => 'Product Lines',
        'registrationUrl' => 'Registration url',
        'requireProof' => 'Require Proof of Registraion',
    ];
    protected $rules = [
        'brandId' => 'required|exists:product_brands,id',
        'lineId' => 'required',
        'registrationUrl' => 'required',
        'requireProof' => 'nullable',
    ];

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        $this->brands = Brand::pluck('name', 'id')->toArray();

        if (!empty($this->warranty->id)) {
            $this->brandId = $this->warranty->brand_id;
            $this->lineId = $this->warranty->product_lines_id;
            $this->registrationUrl = $this->warranty->registration_url;
            $this->requireProof = $this->warranty->require_proof_of_reg;
            $this->lines = Line::where('brand_id',$this->brandId)->pluck('name', 'id');
        }
    }

    /**
     * event handler for brand field update
     */
    public function brandUpdated($name, $value, $recheckValidation = false)
    {
        $this->fieldUpdated($name, $value, $recheckValidation);
        $this->lines = Line::where('brand_id',$value)->pluck('name', 'id');
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (! empty($this->warranty->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new account
     */
    public function store()
    {
        $this->authorize('store', BrandWarranty::class);

        BrandWarranty::create([
            'brand_id' => $this->brandId,
            'product_lines_id' => $this->lineId,
            'registration_url' => $this->registrationUrl,
            'require_proof_of_reg' => $this->requireProof,
        ]);

        $this->alert('success', 'Record created!');
        return redirect()->route('equipment.warranty.index');
    }

    /**
     * Update existing account
     */
    public function update()
    {
        $this->authorize('update', $this->warranty);

        $this->warranty->fill([
            'brand_id' => $this->brandId,
            'product_lines_id' => $this->lineId,
            'registration_url' => $this->registrationUrl,
            'require_proof_of_reg' => $this->requireProof,
        ]);
        $this->warranty->save();

        $this->editRecord = false;
        $this->alert('success', 'Record updated!');
        return redirect()->route('equipment.warranty.index');
    }

    public function delete()
    {
        $this->authorize('delete', $this->warranty);

        if (!$this->warranty->exists) {
            $this->alert('error','Record not found');
            return redirect()->route('equipment.warranty.index');
        }

        $this->warranty->delete();
        $this->alert('success', 'Record deleted!');
        return redirect()->route('equipment.warranty.index');
    }
}
