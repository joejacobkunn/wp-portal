<?php

namespace App\Http\Livewire\Scheduler\Shifts\AHM;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Shifts;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;
    public $warehouseId;
    public $shifts;
    public $type;
    public $shiftList;
    public $editRecord = false;
    public Warehouse $warehouse;
    public $months = [];
    public $shiftData = [];
    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ],
    ];

    protected $listeners = [
        'edit' => 'edit'
    ];

    protected function rules()
    {
        foreach($this->months as $key => $month) {
            foreach($month['days'] as $day => $data) {
                $this->shiftData[$key][$day][0]['status'] = true;
            }
        }
        return [
            'shiftData' => 'required|array',
            'shiftData.*.*.*.shift' => ['required', 'present'],
            'shiftData.*.*.*.slots' => ['required', 'integer', 'min:0'],
        ];
    }
    protected function messages()
    {
        return [
            'shiftData.required' => 'please fill shift info.',
            'shiftData.*.*.*.shift.required' => 'Shift field is required.',
            'shiftData.*.*.*.slots.required' => 'Slots field is required.',
        ];
    }

    public function mount()
    {
        $this->authorize('viewAny', Shifts::class);
        $this->warehouse = Warehouse::find($this->warehouseId);
        $data = [[
            'title' => 'Scheduler',
        ],
        [
            'title' => 'Shifts',
            'route_name' => 'schedule.shift.index'
        ],
        [
            'title' => Str::headline($this->type),
        ]
        ];
        $this->dispatch('setBreadcrumb', $data);


        $this->shifts = Shifts::where(['whse' => $this->warehouseId, 'type' => $this->type])->first();
        if(!$this->shifts) {
            return;
        }
        foreach($this->shifts->shift as $month => $shift) {
            foreach($shift as $day =>$item) {
                $this->months[$month]['days'][$day]['status'] = true;
            }
        }
        $this->shiftData = $this->shifts->shift;
    }

    public function edit()
    {
        //$this->authorize('view', $this->shifts);
        $this->editRecord = true;
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.shifts.ahm.index');
    }

    public function submit()
    {
        //$this->authorize('update', $this->shifts);
        $this->validate();
        Shifts::updateOrCreate(['whse' => $this->warehouseId, 'type' => $this->type], ['shift' => $this->shiftData]);
        $this->alert('success', 'shift updated');
        return redirect()->route('schedule.shift.index', ['whseId' =>  $this->warehouseId,'tab' =>$this->type]);

    }

    public function addShift($month, $day)
    {
        if(!isset($this->shiftData[$month][$day])) {
            $this->shiftData[$month][$day][0]  = ['shift' => null, 'slots' => null];

        }
        $this->shiftData[$month][$day][]  = ['shift' => null, 'slots' => null];

    }

    public function RemoveShift($month, $day)
    {
        if(!isset($this->shiftData[$month] ) ) {
            unset($this->months[$month]['days'][$day]);
            return;
        }
        if(empty($this->shiftData[$month])) {
            unset($this->shiftData[$month]);
            unset($this->months[$month]);
            return;
        }
        $count = count($this->shiftData[$month][$day]);
       if($count <=1 ) {
        unset($this->shiftData[$month][$day]);
        unset($this->months[$month]['days'][$day]);
        return;
       }
       unset($this->shiftData[$month][$day][$count-1]);
    }

    public function cancel()
    {
        $this->resetValidation();
        $this->editRecord = false;
    }

}
