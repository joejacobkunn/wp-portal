<?php
namespace App\Http\Livewire\ServiceArea\Zones\Traits;

use App\Models\Core\Warehouse;
use App\Models\ServiceArea\Zones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait FormRequest
{
    use LivewireAlert;
    public $name;
    public $description;
    public $scheduleOptions = [
        'am' => 'AM',
        'pm' => 'PM',
        'all_day' => 'All Day'
    ];
    public $days = [
        'monday' => ['enabled' => false, 'schedule' => ''],
        'tuesday' => ['enabled' => false, 'schedule' => ''],
        'wednesday' => ['enabled' => false, 'schedule' => ''],
        'thursday' => ['enabled' => false, 'schedule' => ''],
        'friday' => ['enabled' => false, 'schedule' => ''],
        'saturday' => ['enabled' => false, 'schedule' => ''],
        'sunday' => ['enabled' => false, 'schedule' => ''],
    ];

    protected $validationAttributes = [
        'name' => 'Zone Name',
        'description' => 'Description',
        'days' => 'Days',
    ];

    protected function rules()
    {
        return [
            'name' => 'required',
            'description' => 'nullable',
            'days' => 'array'
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

}
