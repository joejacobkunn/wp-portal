<?php

namespace App\Http\Livewire\Scheduler\ZipCode;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\ZipCode\Form\ZipCodeForm;
use App\Models\Scheduler\Zipcode;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Show extends Component
{
    use AuthorizesRequests, LivewireAlert, HasTabs;

    public Zipcode $zipcode;
    public ZipCodeForm $form;

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
        'zipcode-comment-tabs' => [
            'active' => 'comments',
            'links' => [
                'comments' => 'Comments',
                'activity' => 'Activity',
            ],
        ],
    ];

    public function edit()
    {
        $this->editRecord = true;
        $this->form->init($this->zipcode);
        $this->form->setZones($this->zipcode->whse_id);
    }

    public function cancel()
    {
        $this->editRecord = false;
        $this->resetValidation();
        $this->form->reset();
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.zip-code.show');
    }

    public function submit()
    {
        $this->form->update();
        $this->alert('success', 'Record Updated!');
        return redirect()->route('service-area.index', ['tab' => 'zip_code']);
    }

    public function updatedFormZipCode($value)
    {
        $this->form->setZipcodeDescription($value);
    }

    public function delete()
    {
        $this->zipcode->delete();
        $this->alert('success', 'Record deleted !');
        return redirect()->route('service-area.index', ['tab' => 'zip_code']);
    }
}
