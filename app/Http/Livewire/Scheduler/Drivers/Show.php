<?php

namespace App\Http\Livewire\Scheduler\Drivers;

use App\Http\Livewire\Scheduler\Drivers\Form\DriversForm;
use App\Models\Scheduler\StaffInfo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Http\Livewire\Component\Component;

class Show extends Component
{
    use AuthorizesRequests, LivewireAlert;

    public StaffInfo $staffInfo;
    public DriversForm $form;
    public $editRecord  =false;
    public $breadcrumbs = [

    ];
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
    public function mount()
    {

        $this->breadcrumbs = [
            [
            'title' => 'Drivers',
            'route' => 'schedule.driver.index',
            ],
            [
            'title' => $this->staffInfo->user->name,
        ]];
    }

    public function edit()
    {
        $this->editRecord = true;
        $this->form->init($this->staffInfo);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.drivers.show');
    }

    public function cancel()
    {
        $this->editRecord = false;
        $this->resetValidation();
    }

    public function submit()
    {
        $this->form->update();
        $this->alert('success', 'record updated');

    }

    public function delete()
    {
        $this->staffInfo->delete();
        $this->alert('success', 'record deleted');
        return redirect()->route('schedule.driver.index');

    }

}
