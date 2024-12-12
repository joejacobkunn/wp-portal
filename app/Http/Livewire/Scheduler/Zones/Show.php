<?php

namespace App\Http\Livewire\Scheduler\Zones;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Zones\Traits\FormRequest;
use App\Models\Scheduler\Zones;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, FormRequest, HasTabs;
    public  Zones $zone;
    public $editRecord = false;
    public $alertConfig = [];

    public $breadcrumbs = [[
        'title' => 'Service Area',
        'route_name' => 'service-area.index'],
        ['title' => 'Zones']];

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
        $this->breadcrumbs = array_merge($this->breadcrumbs, [['title' => $this->zone->name]]);
        $this->setAlert();
    }
    public function edit()
    {
        $this->formInit($this->zone);
        $this->editRecord = true;
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.zones.show');
    }

    public function submit()
    {
        $this->update();
    }

    public function cancel()
    {
        $this->editRecord = false;
        $this->resetValidation();
        $this->reset(['name', 'description', 'days']);
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
        $this->zone->is_active =  !$this->zone->is_active;
        $this->zone->save();
        $this->setAlert();
    }
}
