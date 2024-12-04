<?php
namespace App\Http\Livewire\Scheduler\Truck;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Truck\Traits\FormRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Scheduler\Truck;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;
    public Truck $truck;
    public $addRecord = false;

    public function mount()
    {
        // $this->authorize('view', Truck::class);
        $this->formInit();
        $this->updateBreadcrumb();
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.index')->extends('livewire-app');
    }

    public function create()
    {
        $this->addRecord =true;
    }

    public function cancel()
    {
        $this->addRecord = false;
    }

    public  function updateBreadcrumb() {
        $newBreadcrumbs = [
                [
                    'title' => 'Warranty Registration',
                ],
                [
                    'title' => 'Brand Configurator',
                    'route_name' => 'equipment.warranty.index',
                ]

        ];
        $this->dispatch('upBreadcrumb', $newBreadcrumbs);
    }
}
