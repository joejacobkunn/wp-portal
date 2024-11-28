<?php
namespace App\Http\Livewire\SalesRepOverride\Traits;

use App\Exports\WarrantyExport;
use App\Imports\WarrantyImport;
use App\Jobs\ProcessWarrantyRecords;
use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\Equipment\Warranty\WarrantyImport\WarrantyImports;
use App\Models\SalesRepOverride\SalesRepOverride;
use App\Models\SX\Customer;
use App\Models\SX\CustomTable;
use App\Models\SX\Operator;
use App\Models\SX\SalesRepOverride as SXSalesRepOverride;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public $customerName;
    public $address;
    public $line;
    public $operator;

    protected $validationAttributes = [
        'customerNumber' => 'Customer Number',
        'shipTo' => 'Ship To',
        'prodLine' => 'Product Line',
        'salesRep' => 'sales Rep',
    ];

    public function rules()
    {
        return [
            'customerNumber' => 'required',
            'shipTo' => 'required',
            'prodLine' => [
                'required',
                Rule::unique('sales_rep_overrides', 'prod_line')->where(function ($query) {
                    return $query->where('customer_number', $this->customerNumber)
                                ->where('ship_to', $this->shipTo);
                })
                ->ignore($this->getSalesRepOverrideId())
            ],
            'salesRep' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'prodLine.unique' => 'The Product Line already exists with the Customer Number same and Ship To.',
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
        $override = SalesRepOverride::create([
            'customer_number' => $this->customerNumber,
            'ship_to' => strtoupper($this->shipTo),
            'prod_line' => strtoupper($this->prodLine),
            'sales_rep' => strtoupper($this->salesRep),
            'last_updated_by' => Auth::user()->id
        ]);

        if(!config('sx.mock')) DB::connection('sx')->insert("insert into pub.sastaz (cono,codeiden,primarykey,secondarykey,codeval,operinit) values(?,?,?,?,?,?)",[40, 'Sales Rep Override',$this->customerNumber.'@'.strtoupper($this->shipTo),strtoupper($this->prodLine),strtoupper($this->salesRep),strtoupper(auth()->user()->sx_operator_id)]);

        return redirect()->route('sales-rep-override.show', $override);
    }

    public function update()
    {
        $this->authorize('update', $this->salesRepOverride);

        $this->salesRepOverride->fill([
            'prod_line' => strtoupper($this->prodLine),
            'sales_rep' => strtoupper($this->salesRep),
            'last_updated_by' => Auth::user()->id
        ]);
        $this->salesRepOverride->save();

        if(!config('sx.mock')) DB::connection('sx')->update("update pub.sastaz set secondarykey = ?, codeval = ?, operinit = ? where cono = ? and codeiden = ? and primarykey = ?",[strtoupper($this->prodLine),strtoupper($this->salesRep),strtoupper(auth()->user()->sx_operator_id),40, 'Sales Rep Override',$this->customerNumber.'@'.strtoupper($this->shipTo)]);

        $this->editRecord = false;
    }

    public function delete()
    {
        $this->authorize('delete', $this->salesRepOverride);

        //DB::connection('sx')->update("update pub.sastaz set secondarykey = ?, codeval = ?, operinit = ? where cono = ? and codeiden = ? and primarykey = ?",[strtoupper($this->prodLine),strtoupper($this->salesRep),strtoupper(auth()->user()->sx_operator_id),40, 'Sales Rep Override',$this->customerNumber.'@'.strtoupper($this->shipTo)]);

        if ( SalesRepOverride::where('id', $this->salesRepOverride->id )->delete() ) {
            $this->alert('success', 'Record deleted!');
            return redirect()->route('sales-rep-override.index');
        }

        $this->alert('error','Record not found');
        return redirect()->route('sales-rep-override.index');
    }

    public function updatedCustomerNumber()
    {
        if(strlen($this->customerNumber) > 2 && !config('sx.mock'))
        {
            $this->customerName = Customer::where('cono', 40)->where('custno',$this->customerNumber)->first()?->name;
            $this->address = '';
            $this->shipTo = '';
        }
        else
            $this->customerName = '';

    }

    public function updatedShipTo()
    {
        if(strlen($this->shipTo) > 2 && !config('sx.mock'))
        {
            $shipto = DB::connection('sx')->select("SELECT addr , city, state, zipcd FROM pub.arss WHERE cono = 40 and LOWER(shipto) = '".strtolower($this->shipTo)."' and custno = ".$this->customerNumber." with(nolock)");
            if(isset($shipto[0])) $this->address = $shipto[0]->addr.', '.$shipto[0]->city.', '.$shipto[0]->state.', '.$shipto[0]->zipcd;
            else $this->address = '';
        }else{
            $this->address = '';
        }
    }

    public function updatedProdLine()
    {
        if(strlen($this->prodLine) > 1 && !config('sx.mock'))
        {
            $prodline = DB::connection('sx')->select("SELECT descrip FROM pub.icsl WHERE cono = 40 and LOWER(prodline) = '".strtolower($this->prodLine)."' with(nolock)");
            if(isset($prodline[0])) $this->line = $prodline[0]->descrip;
            else $this->line = '';
        }else{
            $this->line = '';
        }
    }

    public function updatedSalesRep()
    {
        if(strlen($this->salesRep) > 1 && !config('sx.mock'))
            $this->operator = Operator::where('cono', 40)->where('slsrep',$this->salesRep)->first()?->name;
        else
            $this->operator = '';

    }

    public function notEligibleForSubmit()
    {
        if (!empty($this->salesRepOverride->id)) return false;
        if(empty($this->operator) || empty($this->line) || empty($this->address) || empty($this->customerName)) return true;
        return false;
    }

}
