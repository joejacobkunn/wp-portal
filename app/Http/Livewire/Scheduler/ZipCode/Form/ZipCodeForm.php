<?php
 namespace App\Http\Livewire\Scheduler\ZipCode\Form;

use App\Models\Core\Warehouse;
use App\Models\Scheduler\Zipcode;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ZipCodeForm extends Form
{
    public ?Zipcode $zipcode;

    public $zone;
    public $zip_code;
    public $service=[];
    public $notes;
    public $delivery_rate;
    public $pickup_rate;
    public $is_active = false;

    protected $validationAttributes = [
        'zip_code' => 'Zip Code',
        'service' => 'Service',
        'zone' => 'Zone',
        'delivery_rate' => 'Delivery Rate',
        'pickup_rate' => 'pickup Rate',
        'notes' => 'Note',
        'is_active' => 'Active'
    ];

    protected function rules()
    {    return  [
                    'zip_code' => [
                    'required',
                    'integer',
                    'digits:5',
                    'exists:zip_codes,zipcode',
                    Rule::unique('scheduler_zipcodes', 'zip_code'),
                    ],
            'service' => 'required',
            'zone' => 'required|exists:zones,id',
            'delivery_rate' => 'required|integer',
            'pickup_rate' => 'required|integer',
            'notes' => 'nullable',
            'is_active' => 'nullable',

        ];
    }

    public function store($whseId)
    {
        $validatedData = $this->validate();
        $validatedData['whse_id'] = $whseId;
        $zipcode = Zipcode::create($validatedData);
        return $zipcode;
    }
}
