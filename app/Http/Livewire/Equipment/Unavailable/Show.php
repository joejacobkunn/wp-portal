<?php

namespace App\Http\Livewire\Equipment\Unavailable;

use App\Http\Livewire\Component\Component;
use App\Models\Core\User;
use App\Models\Equipment\UnavailableUnit;

class Show extends Component
{
    public UnavailableUnit $unavailable_unit;
    public $location_modal = false;
    public $current_location;

    public $breadcrumbs = [
        [
            'title' => 'Unavailable Equipments',
            'route_name' => 'equipment.unavailable.index',
        ],
    ];

    protected $listeners = [
        'closeModal',
    ];

    public function mount()
    {
    }

    public function rules()
    {
        return [
            'current_location' => 'required|min:3',
        ];
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.unavailable.show');
    }

    public function getPossessedByProperty()
    {
        $user = User::where('unavailable_equipments_id', $this->unavailable_unit->possessed_by)->first();
        if($user) return $user->name.' - '.strtoupper($this->unavailable_unit->possessed_by);
        else return strtoupper($this->unavailable_unit->possessed_by);
    }

    public function showLocationUpdateModal()
    {
        $this->location_modal = true;
    }

    public function updateLocation()
    {
        $this->validate();
        $this->unavailable_unit->update(['current_location' => $this->current_location]);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->location_modal = false;
    }
}
