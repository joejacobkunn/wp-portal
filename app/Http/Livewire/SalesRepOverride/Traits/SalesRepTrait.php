<?php
namespace App\Http\Livewire\SalesRepOverride\Traits;

use App\Exports\WarrantyExport;
use App\Imports\WarrantyImport;
use App\Jobs\ProcessWarrantyRecords;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Equipment\Warranty\WarrantyImport\WarrantyImports;
use App\Models\SalesRepOverride\SalesRepOverride;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Maatwebsite\Excel\Facades\Excel;

trait SalesRepTrait
{
    use LivewireAlert;
    public $customerNumber;
    public $shipTo;
    public $prodLine;
    public $salesRep;

    protected $validationAttributes = [
        'customerNumber' => 'Customer Number',
        'shipTo' => 'Ship To',
        'prodLine' => 'Product Line',
        'salesRep' => 'sales Rep',
    ];

    public function rules()
    {
        return [
            'customerNumber' => [
                'required',
                Rule::unique('sales_rep_overrides', 'customer_number')->where(function ($query) {
                    return $query->where('ship_to', $this->shipTo)
                                ->where('prod_line', $this->prodLine);
                })
                ->ignore($this->getSalesRepOverrideId()),
            ],
            'shipTo' => 'required',
            'prodLine' => 'required',
            'salesRep' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'customerNumber.unique' => 'The customer number already exists with the same Ship To and Product Line.',
        ];
    }

    protected function getSalesRepOverrideId()
    {
        return property_exists($this, 'salesRepOverride') && $this->salesRepOverride
            ? $this->salesRepOverride->id
            : null;
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();
        if (! empty($this->salesRepOverride->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function formInit()
    {
        if (!empty($this->salesRepOverride->id)) {
            $this->fill([
                'customerNumber' => $this->salesRepOverride->customer_number,
                'shipTo' => $this->salesRepOverride->ship_to,
                'prodLine' => $this->salesRepOverride->prod_line,
                'salesRep' => $this->salesRepOverride->sales_rep,
            ]);
        }
    }

    public function store()
    {
        $this->authorize('store',  new SalesRepOverride());
        $warranty = SalesRepOverride::create([
            'customer_number' => $this->customerNumber,
            'ship_to' => $this->shipTo,
            'prod_line' => $this->prodLine,
            'sales_rep' => $this->salesRep
        ]);
        $this->alert('success', 'Record created!');
        return redirect()->route('sales-rep-override.index');
    }

    public function update()
    {
        $this->authorize('update', $this->salesRepOverride);

        $this->salesRepOverride->fill([
            'customer_number' => $this->customerNumber,
            'ship_to' => $this->shipTo,
            'prod_line' => $this->prodLine,
            'sales_rep' => $this->salesRep,
        ]);
        $this->salesRepOverride->save();

        $this->editRecord = false;
        $this->alert('success', 'Record updated!');
        return redirect()->route('sales-rep-override.index');
    }

    public function delete()
    {
        $this->authorize('delete', $this->salesRepOverride);

        if ( SalesRepOverride::where('id', $this->salesRepOverride->id )->delete() ) {
            $this->alert('success', 'Record deleted!');
            return redirect()->route('sales-rep-override.index');
        }

        $this->alert('error','Record not found');
        return redirect()->route('sales-rep-override.index');
    }
}
