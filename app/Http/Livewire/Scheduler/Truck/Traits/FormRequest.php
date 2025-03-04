<?php
namespace App\Http\Livewire\Scheduler\Truck\Traits;

use App\Models\Scheduler\Truck\BrandWarranty;
use App\Models\Scheduler\Truck;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Core\Location;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

trait FormRequest
{
    use LivewireAlert, WithFileUploads;

    public $truckImage;
    public $serviceTypes = [
        'AHM' => 'AHM',
        'Delivery / Pickup' => 'Delivery / Pickup'
    ];
    protected $validationAttributes = [
        'truck.truck_name' => 'Truck Name',
        'truck.vin_number' => 'VIN Number',
        'truck.service_type' => 'Service Type',
        'truck.shift_type' => 'Shift Type',
        'truck.model_and_make' => 'Model and Make',
        'truck.year' => 'Year',
        'truck.color' => 'Color',
        'truck.notes' => 'Notes',
        'truck.cubic_storage_space' => 'Storage Space',
        'truckImage' => 'Truck Image',
    ];

    protected $messages = [
        'vin_number.unique' => 'The VIN number must be unique.',
        'year.numeric' => 'The year must be a valid number.',
    ];

    protected function rules()
    {
        return [
            'truck.truck_name' => 'required|string|max:255',
            'truck.cubic_storage_space' => 'required|string|max:255',
            'truck.vin_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('trucks', 'vin_number')->ignore(optional($this->truck)->id),
            ],
            'truck.service_type' => 'required|string|max:255',
            'truck.shift_type' => 'required|string|max:255',
            'truck.model_and_make' => 'required|string|max:255',
            'truck.year' => 'required|numeric|digits:4',
            'truck.color' => 'required|string|max:50',
            'truck.notes' => 'nullable|string',
            'truckImage' => 'nullable',
        ];
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {

        if (empty($this->truck->id)) {
            $this->truck = new Truck();
            $this->truck->truck_name = null;
            $this->truck->vin_number = null;
            $this->truck->service_type = null;
            $this->truck->shift_type = null;
            $this->truck->vin_number = null;
            $this->truck->model_and_make = null;
            $this->truck->year = null;
            $this->truck->color = null;
            $this->truck->notes = null;
            $this->truck->cubic_storage_space = null;
        }
    }

    /**
     * Form submission action
     */
    public function submit()
    {

        $this->validate();

        if (! empty($this->truck->id)) {
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
        // $this->authorize('store', Truck::class);


        $this->truck->fill([
            'whse' => $this->activeWarehouse->id,
            'warehouse_short' => $this->whseShort,
        ]);
        $truck = $this->truck->save();

        if ($this->truckImage && !is_string($this->truckImage)) {
            // Clear old media first (optional)
            $this->truck->clearMediaCollection(Truck::DOCUMENT_COLLECTION);

            $this->truck
                ->syncFromMediaLibraryRequest($this->truckImage)
                ->toMediaCollection(Truck::DOCUMENT_COLLECTION);

        }
        $this->alert('success', 'Truck created successfully!');

        return redirect()->route('scheduler.truck.show', $this->truck->id);

    }

    /**
     * Update existing record
     */
    public function update()
    {
        $this->authorize('update', $this->truck);
        $truck = $this->truck->save();
        if ($this->truckImage != null || gettype($this->truckImage) == 'array') {
            $this->truck
                ->syncFromMediaLibraryRequest($this->truckImage)
                ->toMediaCollection(Truck::DOCUMENT_COLLECTION);

        }
        $this->editRecord = false;
        $this->alert('success', 'Record updated!');
        return redirect()->route('scheduler.truck.show', $this->truck->id);
    }

    public function delete()
    {
        $this->authorize('delete', $this->truck);
        $this->truck->delete();
        $this->alert('success', 'Record deleted!');
        return redirect()->route('scheduler.truck.index');
    }

}
