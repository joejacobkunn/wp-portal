<?php

namespace App\Http\Livewire\Scheduler\Truck\Cargo;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Truck\Form\CargoForm;
use App\Models\Product\Category;
use App\Models\Scheduler\CargoConfigurator;
use App\Models\Scheduler\SroEquipmentCategory;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Show extends Component
{
    use AuthorizesRequests, LivewireAlert;

    public CargoConfigurator $cargoConfigurator;
    public CargoForm $cargoForm;
    public $productCategories;
    public $equipmentCategories = [];
    public $editRecord = false;
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

    public function mount()
    {
        $this->authorize('view', $this->cargoConfigurator);

        $this->breadcrumbs=  array_merge($this->breadcrumbs,
        [
            [
                'title' => $this->cargoConfigurator->productCategory->name,
            ]
        ]);
        $this->productCategories = Category::orderBy('name', 'asc')->pluck('name', 'id');
        $this->equipmentCategories = SroEquipmentCategory::orderBy('name', 'asc')->pluck('name', 'id');

    }

    public function edit()
    {
        $this->editRecord = true;
        $this->cargoForm->init($this->cargoConfigurator);
    }


    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.cargo.show');
    }

    public function cancel()
    {
        $this->editRecord = false;
        $this->cargoForm->reset();
        $this->resetValidation();

    }

    public function submit()
    {
        $this->authorize('update', $this->cargoConfigurator);

        $this->cargoForm->update();
        $this->cargoConfigurator->refresh();
        $this->alert('success', 'cargo config updated');
        $this->cancel();
    }

    public function delete()
    {
        $this->authorize('delete', $this->cargoConfigurator);
        $this->cargoConfigurator->delete();
        $this->alert('success', 'Record deleted!');
        return redirect()->route('scheduler.truck.index', ['tab' => 'cargo']);
    }
}
