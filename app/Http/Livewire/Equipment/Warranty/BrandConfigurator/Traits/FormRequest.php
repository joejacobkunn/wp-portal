<?php
namespace App\Http\Livewire\Equipment\Warranty\BrandConfigurator\Traits;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Product\Brand;
use App\Models\Product\Line;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait FormRequest
{
    use LivewireAlert;
    public $brands =[];

    //attributes
    public $brandId;
    public $altName;
    public $prefix;

    protected $validationAttributes = [
        'brandId' => 'Brand',
        'altName' => 'Alt Names',
        'prefix' => 'Brand Prefix',
    ];
    protected $rules = [
        'brandId' => 'required|exists:product_brands,id',
        'altName' => 'required',
        'prefix' => 'required',
    ];

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        $this->brands = Brand::pluck('name', 'id')->toArray();

        if (!empty($this->warranty->id)) {
            $this->brandId = $this->warranty->brand_id;
            $this->altName = $this->warranty->alt_name;
            $this->prefix = $this->warranty->prefix;
        }
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
     * Create new record
     */
    public function store()
    {
        $this->authorize('store', BrandWarranty::class);

        $warranty = BrandWarranty::create([
            'brand_id' => $this->brandId,
            'alt_name' => $this->altName,
            'prefix' => $this->prefix,
            'account_id' => Auth::user()->account_id
        ]);
        $this->alert('success', 'Record created!');
        return redirect()->route('equipment.warranty.index');
    }

    /**
     * Update existing record
     */
    public function update()
    {
        $this->authorize('update', $this->warranty);

        $this->warranty->fill([
            'brand_id' => $this->brandId,
            'alt_name' => $this->altName,
            'prefix' => $this->prefix,
        ]);
        $this->warranty->save();

        $this->editRecord = false;
        $this->alert('success', 'Record updated!');
        return redirect()->route('equipment.warranty.index');
    }

    public function delete()
    {
        $this->authorize('delete', $this->warranty);

        if ( BrandWarranty::where('id', $this->warranty->id )->delete() ) {
            $this->alert('success', 'Record deleted!');
            return redirect()->route('equipment.warranty.index');
        }

        $this->alert('error','Record not found');
        return redirect()->route('equipment.warranty.index');
    }
}
