<?php
namespace App\Http\Livewire\Scheduler\ServiceArea\Zones\Traits;

use App\Models\Core\Warehouse;
use App\Models\Scheduler\Zones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait FormRequest
{
    use LivewireAlert;
    public $name;
    public $description;
    public $showTimeSlotSection;
    public $is_active = 1;

    protected $validationAttributes = [
        'name' => 'Zone Name',
        'description' => 'Description',
    ];

    protected function rules()
    {

        $rules = [
            'name' => 'required',
            'description' => 'nullable',
            'is_active' => 'nullable'
        ];
        return $rules;
    }

    public function store($warehouseId)
    {
        $this->validate();
        $zone = Zones::create([
            'whse_id' => $warehouseId,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active
        ]);

        $this->alert('success','Zone Created');
        return redirect()->route('service-area.zones.show',$zone);
    }

    public function formInit($zone)
    {
        $this->name = $zone?->name;
        $this->description = $zone?->description;
        $this->is_active = $zone?->is_active;
    }

    public function delete()
    {
        $this->authorize('delete', $this->zone);
        if ( Zones::where('id', $this->zone->id )->delete() ) {
            $this->alert('success', 'Record deleted!');
            return redirect()->route('service-area.index');
        }

        $this->alert('error','Record not found');
        return redirect()->route('service-area.index');
    }

    public function update()
    {
        $this->validate();

        $this->zone->fill([
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);
        $this->zone->save();

        $this->alert('success', 'Zone updated!');
        return redirect()->route('service-area.zones.show',$this->zone);
    }

}
