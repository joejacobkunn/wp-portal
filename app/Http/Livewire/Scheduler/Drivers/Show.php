<?php

namespace App\Http\Livewire\Scheduler\Drivers;

use App\Http\Livewire\Scheduler\Drivers\Form\DriversForm;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Http\Livewire\Component\Component;
use App\Models\Core\User;

class Show extends Component
{
    use AuthorizesRequests, LivewireAlert;

    public User $user;
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
    ];
    public function mount()
    {

        $this->breadcrumbs = [
            [
            'title' => 'Drivers',
            'route' => 'schedule.driver.index',
            ],
            [
            'title' => $this->user->name,
        ]];
    }

    public function edit()
    {
        $this->editRecord = true;
        $this->form->init($this->user);
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
        return redirect()->route('schedule.driver.index');
    }

    public function delete()
    {
        $this->user->delete();
        $this->alert('success', 'record deleted');
        return redirect()->route('schedule.driver.index');

    }
    public function addTag()
    {
        if (trim($this->form->skills) !== '') {
            if (!in_array($this->form->skills, $this->form->tags)) {
                array_push($this->form->tags, trim($this->form->skills));
            }
            $this->form->skills = '';
        }
    }

    public function removeTag($index)
    {
        unset($this->form->tags[$index]);
        $this->form->tags = array_values($this->form->tags);
    }
}
