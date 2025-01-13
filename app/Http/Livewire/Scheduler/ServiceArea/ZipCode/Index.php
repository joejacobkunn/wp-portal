<?php

namespace App\Http\Livewire\Scheduler\ServiceArea\ZipCode;

use App\Models\Core\Warehouse;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\ServiceArea\ZipCode\Form\ZipCodeForm;
use App\Models\Scheduler\Zipcode as SchedulerZipcode;
use App\Models\Scheduler\Zones;
use App\Models\ZipCode;

use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;
    public ZipCodeForm $form;
    public $addRecord = false;

    public $warehouseId;
    public Warehouse $warehouse;

    public function create()
    {
        $this->addRecord= true;
        $this->dispatch('setDecription', 'Create New Zip Code');
    }



    public function mount()
    {
        $this->authorize('ViewAny', SchedulerZipcode::class);
        $this->warehouse = Warehouse::find($this->warehouseId);
        $this->form->setZones($this->warehouse->id);
        $data = [[
            'title' => 'Service Area',
            'route_name' => 'service-area.index'
        ],
        [
            'title' => 'Zipcode',
        ]];
        $this->dispatch('setBreadcrumb', $data);
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->form->reset();
    }

    public function submit()
    {
        $this->authorize('store', SchedulerZipcode::class);
        $zip_code = $this->form->store($this->warehouse->id);
        $this->alert('success', 'ZIP Code Created!');
        return redirect()->route('service-area.zipcode.show', $zip_code);
    }

    public function updatedFormZipCode($value)
    {
        $this->form->setZipcodeDescription($value);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.service-area.zip-code.index');
    }

}
