<?php

namespace App\Http\Livewire\Scheduler\ZipCode;

use App\Models\Core\Warehouse;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\ZipCode\Form\ZipCodeForm;
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
        $this->warehouse = Warehouse::find($this->warehouseId);
        $this->form->setZones($this->warehouse->id);
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->resetValidation();
        $this->form->reset();
    }

    public function submit()
    {
        $this->form->store($this->warehouse->id);
        $this->alert('success', 'Record Created!');
        return redirect()->route('service-area.index', ['tab' => 'zip_code']);
    }

    public function updatedFormZipCode($value)
    {
        $this->form->setZipcodeDescription($value);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.zip-code.index');
    }

}
