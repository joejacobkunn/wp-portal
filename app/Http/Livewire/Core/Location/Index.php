<?php

namespace App\Http\Livewire\Core\Location;

use App\Classes\Fortis;
use App\Http\Livewire\Core\Location\Traits\FormRequest;
use App\Models\Core\Account;
use App\Models\Core\Location;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Livewire\Component\Component;


class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public Account $account;

    public $location;

    public $addLocation = false;

    public $viewLocation = false;

    public $fortis_locations = [];

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
        ]
    ];

    public $locations = [
        ['id' => 'Chesterfield', 'name' => 'Chesterfield'],
        ['id' => 'Utica', 'name' => 'Utica'],
        ['id' => 'Ann Arbor', 'name' => 'Ann Arbor'],
        ['id' => 'Farmington Hills', 'name' => 'Farmington Hills'],
        ['id' => 'Livonia', 'name' => 'Livonia'],
        ['id' => 'Clarkston', 'name' => 'Clarkston'],
        ['id' => 'Richmond', 'name' => 'Richmond']
    ];

    protected $listeners = [
        'location:changed' => 'updateLocation'
    ];

    public function mount()
    {
        $this->authorize('viewAny', Location::class);

        $this->formInit();
    }

    public function render()
    {
        return $this->renderView('livewire.core.location.index');
    }

    public function create()
    {
        $this->authorize('store', Location::class);
        $this->fortis_locations = $this->getFortisLocations();
        $this->addLocation = true;
    }

    public function cancel()
    {
        $this->addLocation = false;
        $this->resetExcept('account', 'location');
        $this->formInit();
        $this->resetValidation();
    }

    public function updateLocation($name, $value, $recheckValidation = true)
    {
        $this->fieldUpdated($name, $value, $recheckValidation);
    }


    private function getFortisLocations()
    {
        $locations = [];
        $fortis = new Fortis();
        $data = json_decode($fortis->fetchLocations());

        foreach($data->list as $location)
        {
            $locations[] = ['id' => $location->id, 'name' => $location->name];
        }

        return $locations;
    }
}
