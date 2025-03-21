<?php
 namespace App\Http\Livewire\Scheduler\ServiceArea\ZipCode\Form;

use App\Models\Core\Warehouse;
use App\Models\Scheduler\Zipcode;
use App\Models\Scheduler\Zones;
use App\Models\ZipCode as GeneralZipcode;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Illuminate\Support\Str;

class ZipCodeForm extends Form
{
    public ?Zipcode $zipcode;

    public $zone =[];
    public $zonesList;
    public $zip_code;
    public $service=[];
    public $notes;
    public $delivery_rate;
    public $pickup_rate;
    public $is_active = true;
    public $zipDescription = false;

    public $serviceArray = [
        'at_home_maintenance' => 'At Home Maintenance',
        'delivery_pickup' => 'Delivery/Pickup',
    ];


    protected $validationAttributes = [
        'zip_code' => 'ZIP Code',
        'service' => 'Service',
        'zone' => 'Zone',
        'delivery_rate' => 'Delivery Rate',
        'pickup_rate' => 'Pickup Rate',
        'notes' => 'Note',
        'is_active' => 'Active'
    ];

    protected function rules($whseId)
    {    return  [
        'zip_code' => [
                'required',
                'integer',
                'digits:5',
                'exists:zip_codes,zipcode',
                Rule::unique('scheduler_zipcodes')
                    ->where(fn ($query) => $query->where('whse_id', $whseId))
                    ->ignore($this->getZipcodeId()),
            ],
            'zone' => 'required|array',
            'delivery_rate' => 'required|numeric',
            'pickup_rate' => 'required|numeric',
            'notes' => 'nullable',
            'is_active' => 'nullable',

        ];
    }

    public function init($zipcode)
    {
        $this->zipcode = $zipcode;
        $this->fill($zipcode->toArray());
        $this->setZipcodeDescription($this->zip_code);
        $this->zone = $this->zipcode->zones->pluck('id')->toArray();
    }

    public function setZipcodeDescription($value)
    {
        $zipcode =  GeneralZipcode::where('zipcode', $value)->first();
        if($zipcode) {
         $this->zipDescription = '<i class="fas fa-map-marker-alt"></i> Location : '.$zipcode->city.', '.$zipcode->state;
         return;
        }

        $this->zipDescription = false;
    }

    public function setZones($warehouseId)
    {
        $this->zonesList = Zones::where('whse_id', $warehouseId)->where('is_active', 1)
        ->pluck('name', 'id');

    }

    public function store($whseId)
    {
        $validatedData = $this->validate($this->rules($whseId));
        $validatedData['whse_id'] = $whseId;
        $zipcode = Zipcode::create($validatedData);
        $zipcode->zones()->sync($this->zone);

        return $zipcode;
    }


    public function update()
    {
        $validatedData = $this->validate($this->rules($this->zipcode->whse_id));
        $zipcode = $this->zipcode->update($validatedData);
        $this->zipcode->zones()->sync($this->zone);
    }

    public function getZipcodeId()
    {
        return $this->zipcode?->id ?? 'null';
    }

}

