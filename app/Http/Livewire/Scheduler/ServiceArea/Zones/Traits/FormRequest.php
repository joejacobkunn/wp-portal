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

    public $scheduleOptions = [
        'ahm' => [
            'am' => 'AM',
            'pm' => 'PM',
            'all_day' => 'All Day'
        ],
        'pickup_delivery_shift' => [
             'morning' => '8:00am -12:00pm',
             'noon' => '12:00pm -4:00pm',
             'afternoon' => '4:00pm-7:00pm',
             'all_day' => 'All Day'
        ]
    ];
    public $days = [
        'monday' => ['enabled' => false, 'ahm_shift' => '', 'delivery_pickup_shift' => '', 'pickup_delivery_slot' => '' ],
        'tuesday' => ['enabled' => false, 'ahm_shift' => '', 'delivery_pickup_shift' => '', 'pickup_delivery_slot' => '' ],
        'wednesday' => ['enabled' => false, 'ahm_shift' => '', 'delivery_pickup_shift' => '', 'pickup_delivery_slot' => '' ],
        'thursday' => ['enabled' => false, 'ahm_shift' => '', 'delivery_pickup_shift' => '', 'pickup_delivery_slot' => '' ],
        'friday' => ['enabled' => false, 'ahm_shift' => '', 'delivery_pickup_shift' => '', 'pickup_delivery_slot' => '' ],
        'saturday' => ['enabled' => false, 'ahm_shift' => '', 'delivery_pickup_shift' => '', 'pickup_delivery_slot' => '' ],
        'sunday' => ['enabled' => false, 'ahm_shift' => '', 'delivery_pickup_shift' => '', 'pickup_delivery_slot' => '' ],
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
            'days' => 'array',
            'is_active' => 'nullable'
        ];

        foreach ($this->days as $day => $values) {
            if ($values['enabled']) {
                $rules["days.{$day}.ahm_slot"] = ['required', 'integer', 'min:0'];
                $rules["days.{$day}.pickup_delivery_slot"] = ['required', 'integer', 'min:0'];
                $rules["days.{$day}.ahm_shift"] = ['required', Rule::in(['am', 'pm', 'all_day'])];
                $rules["days.{$day}.delivery_pickup_shift"] = ['required', Rule::in(['morning', 'noon', 'afternoon', 'all_day'])];
            }
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'days.*.ahm_slot.required' => 'The AHM slot field is required.',
            'days.*.pickup_delivery_slot.required' => 'The pickup/delivery slot field is required.',
            'days.*.ahm_shift.required' => 'The AHM Shift field is required.',
            'days.*.delivery_pickup_shift.required' => 'The Delivery Pickup Shift field is required.',
            'days.*.ahm_slot.integer' => 'The AHM slot must be a number.',
            'days.*.pickup_delivery_slot.integer' => 'The pickup/delivery slot must be a number.',
            'days.*.ahm_shift.in' => 'The Shift must be either AM or PM or All Day.',
            'days.*.delivery_pickup_shift.in' => 'Selected shift is invalid.'
        ];
    }

    public function store($warehouseId)
    {
        $this->validate();
        $zone = Zones::create([
            'whse_id' => $warehouseId,
            'name' => $this->name,
            'description' => $this->description,
            'schedule_days' => $this->days,
            'is_active' => $this->is_active
        ]);

        $this->alert('success','Zone Created');
        return redirect()->route('service-area.zones.show',$zone);
    }

    public function updatedDays(){
        $this->showTimeSlotSection =  collect($this->days)->contains(fn($day) => $day['enabled'] === true);
    }

    public function formInit($zone)
    {
        $this->name = $zone?->name;
        $this->description = $zone?->description;
        $this->days = $zone?->schedule_days;
        $this->is_active = $zone?->is_active;
        $this->updatedDays();
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
            'schedule_days' => $this->days,
            'is_active' => $this->is_active,
        ]);
        $this->zone->save();

        $this->alert('success', 'Zone updated!');
        return redirect()->route('service-area.zones.show',$this->zone);
    }

}
