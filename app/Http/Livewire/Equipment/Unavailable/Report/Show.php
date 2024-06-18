<?php

namespace App\Http\Livewire\Equipment\Unavailable\Report;

use App\Models\Core\User;
use App\Helpers\StringHelper;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment\UnavailableUnit;
use App\Http\Livewire\Component\Component;
use App\Models\Equipment\UnavailableReport;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Enums\Equipment\UnavailableReportStatusEnum;

class Show extends Component
{
    use LivewireAlert;

    public UnavailableReport $report;
    public $unavailableEquipments = [];
    public $editEquipmentId;
    public $editEquipmentName;
    public $equipment_modal = false;
    public $location;
    public $notes;
    public $selectedEquipments = [];
    public $editForm = true;

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

        if ($this->report->status == UnavailableReportStatusEnum::Completed->value) {
            $this->editForm = false;

            if (StringHelper::validJson($this->report->data)) {
                $this->selectedEquipments = array_keys(json_decode($this->report->data, true));
            }
        }
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
        $this->resetErrorBag();
    }

    public function updateLocation()
    {
        $this->validate();
        $equipment = UnavailableUnit::find($this->editEquipmentId);
        $equipment->update(['current_location' => $this->location]);
        $this->equipment_modal = false;
        $this->dispatch('equipment-list:refresh');
    }

    public function submitReport()
    {
        if (empty($this->selectedEquipments)) {
            return $this->addError('selectedEquipments', 'Please select atleast one equipment.' );
        }

        $equipmentData = UnavailableUnit::select('id', 'current_location')
            ->whereIn('id', $this->selectedEquipments)
            ->pluck('current_location', 'id')
            ->toArray();

        $this->report->status = UnavailableReportStatusEnum::Completed->value;
        $this->report->data = json_encode($equipmentData);
        $this->report->save();
        $this->editForm = false;

        $this->alert('success', 'Saved changes!');
    }
}
