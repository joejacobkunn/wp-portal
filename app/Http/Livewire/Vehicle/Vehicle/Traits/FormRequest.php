<?php

namespace App\Http\Livewire\Vehicle\Vehicle\Traits;

use Carbon\Carbon;
use App\Models\Core\User;
use App\Models\Core\Affiliate;
use App\Events\User\UserCreated;
use App\Models\Vehicle\Vehicle;
use Illuminate\Support\Facades\DB;
use App\Services\Environment\Domain;
use Illuminate\Support\Facades\Http;


trait FormRequest
{

    protected $validationAttributes = [
        'vehicle.name' => 'Vehicle Name',
        'vehicle.vin' => 'Vehicle VIN',
        'vehicle.type' => 'Vehicle Type',
        'vehicle.license_plate_number' => 'Vehicle Plate Number',
        'vehicle.make' => 'Vehicle Make',
        'vehicle.model' => 'Vehicle Model',
        'vehicle.year' => 'Vehicle Year'
    ];

    protected function rules()
    {
        return [
            'vehicle.name' => 'required',
            'vehicle.vin' => 'required|alpha_num:ascii|min:17|max:17',
            'vehicle.type' => 'required',
            'vehicle.license_plate_number' => 'required',
            'vehicle.make' => 'required',
            'vehicle.model' => 'required',
            'vehicle.year' => 'required|numeric|digits:4|min:1960|max:2100'
        ];
    }


    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        if (empty($this->vehicle->id)) {
            $this->vehicle = new Vehicle();
            $this->vehicle->name = null;
            $this->vehicle->vin = null;
            $this->vehicle->type = null;
            $this->vehicle->license_plate_number = null;
            $this->vehicle->make = null;
            $this->vehicle->model = null;
            $this->vehicle->year = null;
        }else{

        }
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (!empty($this->vehicle->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new user
     */
    public function store()
    {
        $this->authorize('store', Vehicle::class);
        $this->vehicle->account_id = $this->account->id;
        $this->vehicle->save();
        $this->addRecord =false;
        $this->flash('success', 'Vehicle Created!');
        return redirect()->route('vehicle.show', [
            'vehicle' => $this->vehicle->id
        ]);



    }

    /**
     * Update existing user
     */
    public function update()
    {
        $this->authorize('update', $this->vehicle);
        $this->vehicle->save();
        $this->editRecord = false;
        $this->alert('success', 'Vehicle Updated!');
    }

    public function getVINDetails($vin)
    {
        $response = Http::get($this->nhtsa_api_endpoint.$this->vehicle->vin.'?format=json');
        $data = $response->json();
        return $data['Results'][0];
    }
}
