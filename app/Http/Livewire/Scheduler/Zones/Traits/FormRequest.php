<?php
namespace App\Http\Livewire\Scheduler\Zones\Traits;

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

    public $scheduleOptions = [
        'am' => 'AM',
        'pm' => 'PM',
        'all_day' => 'All Day'
    ];
    public $days = [
        'monday' => ['enabled' => false, 'schedule' => '', 'ahm_slot' => '', 'pickup_delivery_slot' => '' ],
        'tuesday' => ['enabled' => false, 'schedule' => '', 'ahm_slot' => '', 'pickup_delivery_slot' => '' ],
        'wednesday' => ['enabled' => false, 'schedule' => '', 'ahm_slot' => '', 'pickup_delivery_slot' => '' ],
        'thursday' => ['enabled' => false, 'schedule' => '', 'ahm_slot' => '', 'pickup_delivery_slot' => '' ],
        'friday' => ['enabled' => false, 'schedule' => '', 'ahm_slot' => '', 'pickup_delivery_slot' => '' ],
        'saturday' => ['enabled' => false, 'schedule' => '', 'ahm_slot' => '', 'pickup_delivery_slot' => '' ],
        'sunday' => ['enabled' => false, 'schedule' => '', 'ahm_slot' => '', 'pickup_delivery_slot' => '' ],
    ];

    protected $validationAttributes = [
        'name' => 'Zone Name',
        'description' => 'Description',
        'days' => 'Days',
    ];

    protected function rules()
    {

        $rules = [
            'name' => 'required',
            'description' => 'nullable',
            'days' => 'array'
        ];

        foreach ($this->days as $day => $values) {
            if ($values['enabled']) {
                $rules["days.{$day}.ahm_slot"] = ['required', 'integer', 'min:0'];
                $rules["days.{$day}.pickup_delivery_slot"] = ['required', 'integer', 'min:0'];
                $rules["days.{$day}.schedule"] = ['required', Rule::in(['am', 'pm', 'all_day'])];
            }
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'days.*.ahm_slot.required' => 'The AHM slot field is required.',
            'days.*.pickup_delivery_slot.required' => 'The pickup/delivery slot field is required.',
            'days.*.schedule.required' => 'The Shift field is required.',
            'days.*.ahm_slot.integer' => 'The AHM slot must be a number.',
            'days.*.pickup_delivery_slot.integer' => 'The pickup/delivery slot must be a number.',
            'days.*.schedule.in' => 'The Shift must be either AM or PM or All Day.'
        ];
    }

    public function store($warehouseId)
    {
        $this->validate();
        $inventory = Zones::create([
            'whse_id' => $warehouseId,
            'name' => $this->name,
            'description' => $this->description,
            'schedule_days' => $this->days
        ]);

        $this->alert('success','Zone Record Created');
        return redirect()->route('service-area.index');
    }

    public function updatedDays(){
        $this->showTimeSlotSection =  collect($this->days)->contains(fn($day) => $day['enabled'] === true);
    }

    public function formInit($zone)
    {
        $this->name = $zone?->name;
        $this->description = $zone?->description;
        $this->days = $zone?->schedule_days;
        $this->updatedDays();
    }

    public function delete()
    {

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
            'schedule_days' => $this->days,
        ]);
        $this->zone->save();

        $this->alert('success', 'Record updated!');
        return redirect()->route('service-area.index');
    }

}
