<?php
namespace App\Http\Livewire\Scheduler\Truck\Traits;

use App\Models\Scheduler\Truck\BrandWarranty;
use App\Models\Scheduler\Truck;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Core\Location;

trait FormRequest
{
    use LivewireAlert;

    protected $validationAttributes = [
        'truck.truck_name' => 'Truck Name',
        'truck.vin_number' => 'VIN Number',
        'truck.model_and_make' => 'Model and Make',
        'truck.year' => 'Year',
        'truck.color' => 'Color',
        'truck.notes' => 'Notes',
        'truck.driver' => 'Driver',
        'truck.cubic_storage_space' => 'Storage Space',
    ];

    protected $messages = [
        'vin_number.unique' => 'The VIN number must be unique.',
        'year.numeric' => 'The year must be a valid number.',
    ];

    protected function rules()
    {
        return [
            'truck.truck_name' => 'required|string|max:255',
            'truck.driver' => 'required|numeric|exists:users,id',
            'truck.cubic_storage_space' => 'required|string|max:255',
            'truck.vin_number' => 'required|string|max:255|unique:trucks,vin_number',
            'truck.model_and_make' => 'required|string|max:255',
            'truck.year' => 'required|numeric|digits:4',
            'truck.color' => 'required|string|max:50',
            'truck.notes' => 'nullable|string',
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
            $this->truck->model_and_make = null;
            $this->truck->year = null;
            $this->truck->color = null;
            $this->truck->notes = null;
            $this->truck->driver = null;
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
            'whse' => $this->whseId,
        ]);
        $this->truck->save();
        $this->alert('success', 'Truck created successfully!');
        return redirect()->route('scheduler.truck.index', ['whseId' => $this->whseId]);
    }

    /**
     * Update existing record
     */
    public function update()
    {
        $this->authorize('update', $this->truck);
        $this->truck->save();

        $this->editRecord = false;
        $this->alert('success', 'Record updated!');
        return redirect()->route('scheduler.truck.index', ['whseId' => $this->whseId]);
    }

}
