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

    public $zone;
    public $zones;
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
    public $shiftOptions = [
        'ahm' => [
            'am' => '9 AM - 1 PM',
            'pm' => '1 PM - 6 PM',
            'all_day' => 'All Day'
        ],
        'pickup_delivery_shift' => [
             'morning' => '8:00 AM -12:00 PM',
             'noon' => '12:00 PM -4:00 PM',
             'afternoon' => '4:00 AM-7:00 PM',
             'all_day' => 'All Day'
        ]
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

    protected function rules()
    {    return  [
            'zip_code' => [
                'required',
                'integer',
                'digits:5',
                'exists:zip_codes,zipcode',
                Rule::unique('scheduler_zipcodes', 'zip_code')->ignore($this->getZipcodeId()),
            ],
            'service' => 'required',
            'zone' => 'required|exists:zones,id',
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
        $this->zones = Zones::where('whse_id', $warehouseId)->where('is_active', 1)
        ->pluck('name', 'id');
    }

    public function store($whseId)
    {
        $validatedData = $this->validate();
        $validatedData['whse_id'] = $whseId;
        $zipcode = Zipcode::create($validatedData);
        return $zipcode;
    }


    public function update()
    {
        $validatedData = $this->validate();
        $zipcode = $this->zipcode->update($validatedData);
        return $zipcode;
    }

    public function getZipcodeId()
    {
        return $this->zipcode?->id ?? 'null';
    }

    public function getHint($value)
    {
        $this->zone = $value;
        $zone = Zones::find($value);
        $out = '';
        foreach ($zone->schedule_days as $day => $details) {
            if ($details['enabled']) {
                $ahmShift = $details['ahm_shift'];
                $pickupDeliveryShift = $details['delivery_pickup_shift'];

                $out .= strtoupper($day) . ' : (AHM SHIFT: ' . $this->shiftOptions['ahm'][$ahmShift] . ') (PICKUP/DELIVERY SHIFT: ' . $this->shiftOptions['pickup_delivery_shift'][$pickupDeliveryShift] . '), ';            }
        }
        return rtrim($out, ', ');
    }
}

