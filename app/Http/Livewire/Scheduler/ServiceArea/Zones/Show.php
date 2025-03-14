<?php

namespace App\Http\Livewire\Scheduler\ServiceArea\Zones;

use App\Enums\Scheduler\ScheduleTypeEnum;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\ServiceArea\Zones\Traits\FormRequest;
use App\Models\Scheduler\Zones;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, FormRequest, HasTabs;
    public  Zones $zone;
    public $editRecord = false;
    public $alertConfig = [];
    public $serviceOptions;

    public $breadcrumbs = [];

    protected $listeners = [
        'edit' => 'edit',
        'deleteRecord' => 'delete',
        'updateStatus' => 'updateStatus',
    ];
    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => 'Confirm Delete',
            'listener' => 'deleteRecord',
        ],
    ];

    public $tabs = [
        'zones-comment-tabs' => [
            'active' => 'comments',
            'links' => [
                'comments' => 'Comments',
                'activity' => 'Activity',
            ],
        ],
    ];

    public function mount()
    {
        $this->authorize('view', $this->zone);
        $serviceRoute =  route('service-area.index').'?whseId='.$this->zone->whse_id.'&tab=zones';
        $this->breadcrumbs =  [[
            'title' => 'Service Area',
            'href' => $serviceRoute,
        ],
        ['title' => 'Zones'],
        ['title' => $this->zone->name]];
        $this->setAlert();
        $this->serviceOptions = collect(ScheduleTypeEnum::cases())
        ->mapWithKeys(fn($case) => [$case->name => $case->label()])
        ->toArray();
    }
    public function edit()
    {
        $this->formInit($this->zone);
        $this->editRecord = true;
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.service-area.zones.show');
    }

    public function submit()
    {
        $this->authorize('update', $this->zone);
        $this->update();
    }

    public function cancel()
    {
        $this->editRecord = false;
        $this->resetValidation();
        $this->reset(['name', 'description', 'service']);
    }

    public function setAlert()
    {
        if($this->zone->is_active) {
            $this->alertConfig['level'] = 'success';
            $this->alertConfig['message'] = 'This zone is active';
            $this->alertConfig['icon'] = 'fa-check-circle';
            $this->alertConfig['btnClass'] = 'btn-outline-danger';
            $this->alertConfig['btnText'] = 'Deactivate';
        } else {
            $this->alertConfig['level'] = 'danger';
            $this->alertConfig['message'] = 'This zone is deactivated';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['btnClass'] = 'btn-outline-primary';
            $this->alertConfig['btnText'] = 'Activate';
        }
    }

    public function updateStatus()
    {
        $this->authorize('update', $this->zone);
        $this->zone->is_active =  !$this->zone->is_active;
        $this->zone->save();
        $this->setAlert();
    }
}
