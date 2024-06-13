<?php

namespace App\Http\Livewire\Equipment\Unavailable\Report;

use App\Http\Livewire\Component\Component;
use App\Models\Core\User;
use App\Models\Equipment\UnavailableReport;
use App\Models\Equipment\UnavailableUnit;

class Show extends Component
{

    public UnavailableReport $report;
    public $unavailableEquipments = [];
    public $editEquipmentId;
    public $editEquipmentName;
    public $equipment_modal = false;
    public $location;

    public $breadcrumbs = [
        [
            'title' => 'All Reports',
            'route_name' => 'equipment.unavailable.report.index',
        ],
        [
            'title' => 'Report',
        ],

    ];

    protected $listeners = [
        'closeModal',
        'equipment-list:refresh' => 'refresh'
    ];

    public function rules()
    {
        return [
            'location' => 'required|min:3',
        ];
    }



    public function mount()
    {
        $this->unavailableEquipments = UnavailableUnit::where('possessed_by', User::find($this->report->user_id)->unavailable_equipments_id)->where('is_unavailable', 1)->get();
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.unavailable.report.show');
    }

    public function closeModal()
    {
        $this->equipment_modal = false;
    }

    public function updateEquipmentLocation($equipmentId)
    {
        $equipment = UnavailableUnit::find($equipmentId);
        $this->editEquipmentId = $equipmentId;
        $this->editEquipmentName = $equipment->product_name;
        $this->location = $equipment->current_location;
        $this->equipment_modal = true;
    }

    public function updateLocation()
    {
        $this->validate();
        $equipment = UnavailableUnit::find($this->editEquipmentId);
        $equipment->update(['current_location' => $this->location]);
        $this->equipment_modal = false;
        $this->dispatch('equipment-list:refresh');
    }
}
