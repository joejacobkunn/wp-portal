<?php

namespace App\Http\Livewire\Scheduler\Shifts\AHM;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Shifts;
use Carbon\Carbon;

class Index extends Component
{
    public $warehouseId;
    public $shifts;
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
    public function mount()
    {
        $this->warehouse = Warehouse::find($this->warehouseId);
        $data = [[
            'title' => 'Scheduler',
        ],
        [
            'title' => 'Shifts',
            'route_name' => 'schedule.shift.index'
        ],
        [
            'title' => 'AHM',
        ]];


        $this->shifts = Shifts::where(['whse' => 4, 'type' => 'ahm'])->first();
        $this->dispatch('setBreadcrumb', $data);

    }

    public function edit()
    {
        $this->editRecord = true;
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.shifts.ahm.index');
    }

    public function submit()
    {
        Shifts::updateOrCreate(['whse' => $this->warehouseId, 'type' => 'ahm'], ['shift' => $this->shiftData]);
    }

    public function addShift($month, $day)
    {
        if(!isset($this->shiftData[$month][$day])) {
            $this->shiftData[$month][$day][0]  = ['shift' => null, 'slots' => null];

        }
        $this->shiftData[$month][$day][]  = ['shift' => null, 'slots' => null];
    }

}
