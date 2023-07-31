<?php

namespace App\Http\Livewire\Vehicle\Vehicle;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Vehicle\Vehicle\Traits\FormRequest;
use App\Models\Core\Account;
use App\Models\Vehicle\Vehicle;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests;
    use FormRequest;
    use LivewireAlert;

    public $nhtsa_api_endpoint = 'https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/';

    public $vehicle;

    public $account;

    public $addRecord = false;

    public $valid_vin = false;

    public $vehicle_types = [
        'box-truck' => 'Box Truck',
        'pickup-truck' => 'Pickup Truck',
        'trailer' => 'Trailer',
        'gooseneck-trailer' => 'Gooseneck Trailer',
    ];

    public $breadcrumbs = [
        [
            'title' => 'Vehicles',
        ],
    ];

    public function mount()
    {
        $this->account = account();
        $this->vehicle = new Vehicle();
        $this->formInit();
    }

    public function render()
    {
        //$this->authorize('viewAny', Account::class);

        return $this->renderView('livewire.vehicle.vehicle.index');
    }

    public function updated($propertyName, $value)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        //$this->authorize('store', Account::class);
        $this->addRecord = true;
    }

    /**
     * Form cancel action
     */
    public function cancel()
    {
        $this->resetExcept('account');
        $this->resetValidation();
        $this->addRecord = false;
    }

    public function updatedVehicleLicensePlateNumber($value)
    {
        $this->vehicle->license_plate_number = strtoupper($value);
    }

    public function updatedVehicleVin($value)
    {
        $this->vehicle->vin = strtoupper($value);
        $vin_details = $this->getVINDetails($this->vehicle->vin);
        if ($vin_details['Make'] && $vin_details['Model']) {
            $this->valid_vin = true;
        } else {
            $this->valid_vin = false;
        }
        $this->vehicle->make = trim($vin_details['Make']);
        $this->vehicle->model = trim($vin_details['Model']);
        $this->vehicle->year = trim($vin_details['ModelYear']);
        $this->vehicle->type = trim($vin_details['VehicleType']);
    }

    public function updatingVehicleVin($value)
    {
        if (strlen($value) != 17) {
            $this->valid_vin = false;
        }
    }

    public function changeVIN()
    {
        $this->vehicle->vin = null;
        $this->vehicle->make = null;
        $this->vehicle->model = null;
        $this->vehicle->year = null;
        $this->vehicle->type = null;
        $this->valid_vin = false;

    }
}
