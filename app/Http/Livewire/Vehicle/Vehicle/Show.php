<?php

namespace App\Http\Livewire\Vehicle\Vehicle;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Vehicle\Vehicle\Traits\FormRequest;
use App\Models\Vehicle\Vehicle;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Show extends Component
{
    use AuthorizesRequests, FormRequest,LivewireAlert;

    public $nhtsa_api_endpoint = 'https://vpic.nhtsa.dot.gov/api/vehicles/decodevinvalues/';

    public Vehicle $vehicle;

    public $editRecord = false;

    public $retire_modal = false;

    public $valid_vin = true;

    public $vehicle_types = [
        'box-truck' => 'Box Truck',
        'pickup-truck' => 'Pickup Truck',
        'trailer' => 'Trailer',
        'gooseneck-trailer' => 'Gooseneck Trailer',
    ];

    public $breadcrumbs = [
        [
            'title' => 'Vehicles',
            'route_name' => 'vehicle.index',
        ],
    ];

    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => 'Confirm Delete',
            'confirm_message' => 'Are you sure you want to delete this vehicle?',
            'confirm_button_text' => 'Delete',
            'listener' => 'deleteRecord',
        ],
    ];

    protected $listeners = [
        'edit',
        'deleteRecord',
        'closeModal',

    ];

    public function mount()
    {
        array_push($this->breadcrumbs, ['title' => $this->vehicle->name]);

    }

    public function render()
    {
        return $this->renderView('livewire.vehicle.vehicle.show');
    }

    public function updated($propertyName, $value)
    {
        $this->validateOnly($propertyName);
    }

    public function edit()
    {
        $this->editRecord = true;
    }

    public function deleteRecord()
    {
        $this->authorize('delete', $this->vehicle);
        $this->vehicle->delete();
        $this->flash('success', 'Vehicle Deleted !');

        return redirect()->route('vehicle.index');

    }

    public function retire()
    {
        $this->authorize('delete', $this->vehicle);
        $this->vehicle->retired_at = now();
        $this->vehicle->save();
        $this->retire_modal = false;
        $this->alert('warning', 'Vehicle has been retired');
    }

    public function activate()
    {
        $this->authorize('delete', $this->vehicle);
        $this->vehicle->retired_at = null;
        $this->vehicle->save();
        $this->alert('success', 'Vehicle has been activated');
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
        $this->vehicle->make = $vin_details['Make'];
        $this->vehicle->model = $vin_details['Model'];
        $this->vehicle->year = $vin_details['ModelYear'];
        $this->vehicle->type = $vin_details['VehicleType'];
    }

    public function updatingVehicleVin($value)
    {
        if (strlen($value) != 17) {
            $this->valid_vin = false;
        }
    }

    public function closeModal()
    {
        $this->retire_modal = false;
    }

    public function cancel()
    {
        $this->vehicle->setRawAttributes($this->vehicle->getOriginal());
        $this->resetValidation();
        $this->editRecord = false;

    }
}
