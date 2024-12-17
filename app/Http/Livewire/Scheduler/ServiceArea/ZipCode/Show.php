<?php

namespace App\Http\Livewire\Scheduler\ServiceArea\ZipCode;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\ServiceArea\ZipCode\Form\ZipCodeForm;
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
    public $zoneHint;
    public $alertConfig = [];
    public $breadcrumbs = [[
        'title' => 'Service Area',
        'route_name' => 'service-area.index'],
        ['title' => 'Zipcode']];

    protected $listeners = [
        'edit' => 'edit',
        'deleteRecord' => 'delete',
        'updateStatus' => 'updateStatus',
        'setHint' => 'setZoneHint'

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

    public function mount()
    {
        $this->authorize('view', $this->zipcode);
        $serviceRoute =  route('service-area.index').'?whseId='.$this->zipcode->whse_id.'&tab=zip_code';
        $this->breadcrumbs =  [[
            'title' => 'Service Area',
            'href' => $serviceRoute,
        ],
        ['title' => 'Zipcodes'],
        ['title' => $this->zipcode->zip_code]];
        $this->setAlert();
    }

    public function edit()
    {
        $this->editRecord = true;
        $this->form->init($this->zipcode);
        $this->form->setZones($this->zipcode->whse_id);
        $this->setZoneHint('zones', $this->zipcode->zone);
    }

    public function cancel()
    {
        $this->editRecord = false;
        $this->resetValidation();
        $this->form->reset();
    }


    public function render()
    {
        return $this->renderView('livewire.scheduler.service-area.zip-code.show');
    }

    public function submit()
    {
        $this->authorize('update', $this->zipcode);
        $this->form->update();
        $this->alert('success', 'Record Updated!');
        return redirect()->route('service-area.zipcode.show', $this->zipcode);
    }

    public function updatedFormZipCode($value)
    {
        $this->form->setZipcodeDescription($value);
    }

    public function delete()
    {
        $this->authorize('delete', $this->zipcode);
        $this->zipcode->delete();
        $this->alert('success', 'Record deleted !');
        return redirect()->route('service-area.index', ['tab' => 'zip_code']);
    }

    public function setAlert()
    {
        if($this->zipcode->is_active) {
            $this->alertConfig['level'] = 'success';
            $this->alertConfig['message'] = 'This zipcode is active';
            $this->alertConfig['icon'] = 'fa-check-circle';
            $this->alertConfig['btnClass'] = 'btn-outline-danger';
            $this->alertConfig['btnText'] = 'Deactivate';
        } else {
            $this->alertConfig['level'] = 'danger';
            $this->alertConfig['message'] = 'This zipcode is deactivated';
            $this->alertConfig['icon'] = 'fa-times-circle';
            $this->alertConfig['btnClass'] = 'btn-outline-primary';
            $this->alertConfig['btnText'] = 'Activate';
        }
    }

    public function updateStatus()
    {
        $this->zipcode->is_active =  !$this->zipcode->is_active;
        $this->zipcode->save();
        $this->setAlert();
    }

    public function setZoneHint($key, $value)
    {
        $this->zoneHint = $this->form->getHint($value);
    }
}
