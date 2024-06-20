<?php

namespace App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Traits;

use App\Models\Equipment\Warranty\BrandWarranty;
use App\Models\Product\Brand;
use App\Models\Product\Line;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait FormRequest
{
    use LivewireAlert;
    public $brands =[];
    public $lines = [];

    protected $validationAttributes = [
        'warranty.brand_id' => 'Brand',
        'warranty.registration_url' => 'Registration url',
        'warranty.product_lines_id' => 'Product Lines',
        'warranty.require_proof_of_reg' => 'Require Proof of Registraion',
    ];



    protected function rules()
    {
        return [
            'warranty.brand_id' => 'required|exists:product_brands,id',
            'warranty.registration_url' => 'required|min:5',
            'warranty.product_lines_id' => 'required',
            'warranty.require_proof_of_reg' => 'nullable',
        ];
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        $this->brands= Brand::pluck('name', 'id')->toArray();
        if(!empty($this->warranty->brand_id)){
            $this->lines = Line::where('brand_id',$this->warranty->brand_id)->pluck('name', 'id');
        }

        if (empty($this->warranty->id)) {
            $this->warranty = new BrandWarranty();
            $this->warranty->brand_id = null;
            $this->warranty->registration_url = null;
            $this->warranty->product_lines_id = null;
            $this->warranty->require_proof_of_reg = null;

        }
    }


    /**
     * event handler for brand field update
     */
    public function brandUpdated($name, $value, $recheckValidation = false)
    {
        $this->fieldUpdated($name, $value, $recheckValidation);
        $this->linesDisabled=false;
        $this->lines = Line::where('brand_id',$value)->pluck('name', 'id');
    }
    public function linesUpdated($name, $value, $recheckValidation = false)
    {
        $this->fieldUpdated($name, $value, $recheckValidation);
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
        $this->warranty->save();

        session()->flash('success', 'Record created!');

        return redirect()->route('equipment.warranty.index');
    }

    /**
     * Update existing account
     */
    public function update()
    {
        $this->authorize('update', $this->warranty);

        $this->warranty->save();
        $this->editRecord = false;
        session()->flash('success', 'Record updated!');
        return redirect()->route('equipment.warranty.index');
    }

    public function delete(){
        $this->authorize('delete', $this->warranty);
        $record  = BrandWarranty::find($this->warranty->id);

        if($record){
            $this->warranty->delete();
            session()->flash('success', 'Record deleted!');

        }else{
           // session()->flash('error', 'Record not found!');
           $this->alert('error','Record not found');
        }
        return redirect()->route('equipment.warranty.index');
    }

}
