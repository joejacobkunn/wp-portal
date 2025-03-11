<?php

namespace App\Http\Livewire\Scheduler\Truck\Cargo;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Truck\Form\CargoForm;
use App\Models\Product\Category;
use App\Models\Scheduler\CargoConfigurator;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use LivewireAlert;
    public CargoForm $cargoForm;
    public $addRecord;
    public $productCategories = [];
    public $breadcrumbs = [
        [
            'title' => 'Scheduler',
        ],
        [
            'title' => 'Trucks',
            'route_name' => 'scheduler.truck.index',
        ],
        [
            'title' => 'Cargo',
            'route_name' => 'scheduler.truck.cargo.index',

        ],

    ];
    public function mount()
    {
        $this->authorize('viewAny', CargoConfigurator::class);

        $this->productCategories = Category::orderBy('name', 'asc')->pluck('name', 'id');
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.cargo.index');
    }

    public function create()
    {
        $this->addRecord =true;
    }

    public function cancel()
    {
        $this->addRecord = false;
        $this->cargoForm->reset();
        $this->resetValidation();
    }

    public function submit()
    {
        $this->authorize('store', CargoConfigurator::class);
        $this->cargoForm->store();
        $this->alert('success', 'New Cargo Config Added');
        $this->cancel();
    }
}
