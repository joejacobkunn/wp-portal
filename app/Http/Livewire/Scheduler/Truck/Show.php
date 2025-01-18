<?php

namespace App\Http\Livewire\Scheduler\Truck;

use App\Models\Scheduler\Truck;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Truck\Traits\FormRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, LivewireAlert, FormRequest;

    public Truck $truck;

    public $editRecord = false;

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

    protected $listeners = [
        'deleteRecord' =>'delete',
        'edit' =>'edit'
    ];

    public function edit()
    {
        $this->editRecord = true;
    }

    public function mount()
    {
        $this->authorize('view', $this->truck);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.show');
    }
}
