<?php
namespace App\Http\Livewire\Scheduler\Truck;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\Truck\Traits\FormRequest;
use App\Models\Core\User;
use App\Models\Core\Warehouse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Scheduler\Truck;
use App\Traits\HasTabs;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use AuthorizesRequests, HasTabs;

    public $warehouses;
    public $activeWarehouse;
    public $whseShort;
    public $breadcrumbs = [
        [
            'title' => 'Scheduler',
        ],
        [
            'title' => 'Trucks',
            'route_name' => 'scheduler.truck.index',
        ]

    ];
    public $tabs = [
        'truck-tabs' => [
            'active' => 'trucks',
            'links' => [
                'trucks' => 'Truck List',
                'cargo' => 'Cargo Configurator',
    ]]];

    protected $queryString = [
        'whseShort' => ['except' => '', 'as' => 'whse'],
        'tabs.truck-tabs.active' => ['except' => '', 'as' => 'tab'],
    ];
    public function mount()
    {

        $this->warehouses = Warehouse::where('cono', 10)->orderBy('title')->get();

        $this->whseShort =  $this->whseShort ? $this->whseShort : Warehouse::where('title', Auth::user()->office_location)->first()->short;
        $this->activeWarehouse = $this->warehouses->where('short', $this->whseShort)->first();
    }



    public function changeWarehouse($whseShort)
    {
        $this->activeWarehouse = Warehouse::where('short' , $whseShort)->first();
        $this->whseShort = $this->activeWarehouse->short;
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.index')->extends('livewire-app');
    }
}
