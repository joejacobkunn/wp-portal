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
    protected $listeners = [
        'edit' => 'edit',
        'deleteRecord' => 'delete',
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
}
