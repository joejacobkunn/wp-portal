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
    public $zipDescription = false;
    public Warehouse $warehouse;
    public $zones;

    public function create()
    {
        $this->addRecord= true;
        $this->dispatch('setDecription', 'Create New Zip Code');
    }

    public function mount()
    {
        $this->warehouse = Warehouse::find($this->warehouseId);
        $this->zones = Zones::where('whse_id', $this->warehouse->id)
                        ->pluck('name', 'id');
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

    public function render()
    {
        return $this->renderView('livewire.scheduler.zip-code.index');
    }

    public function updatedFormZipCode($value)
    {
       $zipcode =  ZipCode::where('zipcode', $value)->first();
       if($zipcode) {
        $this->zipDescription = 'This zip code belongs to '.$zipcode->city.', '.$zipcode->state.'.';
        return;
       }

       $this->zipDescription = 'Entered Zipcode not found in our database';
    }
}
