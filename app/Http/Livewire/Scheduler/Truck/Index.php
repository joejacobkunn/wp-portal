<?php
namespace App\Http\Livewire\Scheduler\Truck;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Truck\Traits\FormRequest;
use App\Models\Core\User;
use App\Models\Core\Warehouse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Scheduler\Truck;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;
    public Truck $truck;
    public $addRecord = false;
    public $warehouses;
    public $drivers;
    public $activeWarehouse;
    public $whseId;
    public $breadcrumbs = [
        [
            'title' => 'Scheduler',
        ],
        [
            'title' => 'Trucks',
            'route_name' => 'scheduler.truck.index',
        ]

    ];
    protected $queryString = [
        'whseId' => ['except' => '', 'as' => 'whseId'],
    ];
    public function mount()
    {
        // $this->authorize('view', Truck::class);
        $this->formInit();
        $this->warehouses = Warehouse::where('cono', 10)->orderBy('title')->get();

        $this->whseId =  $this->whseId ? $this->whseId : Warehouse::where('title', Auth::user()->office_location)->first()->id;

        $this->activeWarehouse = Warehouse::where('cono', 10)->where('id', $this->whseId)->first();
        $this->drivers = User::where('title', 'driver')->get();
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

    public function changeWarehouse($whseId)
    {
        $this->activeWarehouse = Warehouse::find($whseId);
        $this->whseId = $this->activeWarehouse->id;
    }
}
