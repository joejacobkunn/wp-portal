<?php

namespace App\Http\Livewire\Scheduler\NotificationTemplate;

use App\Http\Livewire\Scheduler\NotificationTemplate\Form\NotificationForm;
use App\Models\Scheduler\NotificationTemplate;
use App\Http\Livewire\Component\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Show extends Component
{
    use LivewireAlert;
    public $editRecord;
    public NotificationTemplate $template;
    public NotificationForm $form;
    public $breadcrumbs = [
        [
            'title' => 'Scheduler',
        ],
        [
            'title' => 'Notification Templates',
            'route_name' => 'schedule.email-template.index',
        ]
    ];
    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ]
    ];

    protected $listeners = [
        'edit' => 'edit',
    ];

    public function edit()
    {
        $this->editRecord = true;
        $this->form->init($this->template);
    }
    public function render()
    {
        return $this->renderView('livewire.scheduler.notification-template.show');
    }

    public function toggleStatus()
    {
        $this->template->is_active =  !$this->template->is_active;
        $this->template->save();
    }

    public function submit()
    {
        $this->form->update();
        $this->alert('success', 'Record updated');
        return redirect()->route('schedule.email-template.index');

    }

    public function cancel()
    {
        $this->resetValidation();
        $this->editRecord = false;
    }
}
