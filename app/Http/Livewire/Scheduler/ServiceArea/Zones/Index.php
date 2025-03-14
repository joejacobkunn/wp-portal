<?php

namespace App\Http\Livewire\Scheduler\ServiceArea\Zones;

use App\Enums\Scheduler\ScheduleTypeEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\ServiceArea\Zones\Traits\FormRequest;
use App\Http\Livewire\Scheduler\Zones\Form\ZonesForm;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Zones;

class Index extends Component
{
    use FormRequest;
    public $addRecord =false;
    public $warehouseId;
    public Warehouse $warehouse;
    public $serviceOptions;


    public function mount()
    {
        $this->authorize('viewAny', Zones::class);
        $this->warehouse = Warehouse::find($this->warehouseId);
        $data = [[
            'title' => 'Service Area',
            'route_name' => 'service-area.index'
        ],
        [
            'title' => 'Zones',
            ]];

        $this->dispatch('setBreadcrumb', $data);
        $this->serviceOptions = collect(ScheduleTypeEnum::cases())
        ->mapWithKeys(fn($case) => [$case->name => $case->label()])
        ->toArray();
    }

    public function create()
    {
        $this->addRecord = true;
        $this->dispatch('setDecription', 'Create New Zone');
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.service-area.zones.index');
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->reset(['name', 'description']);
    }

    public function submit()
    {
        $this->authorize('store', Zones::class);
        $this->store($this->warehouseId);
    }
}
