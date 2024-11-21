<?php

namespace App\Http\Livewire\Pwa;


use App\Classes\Fortis;
use App\Models\Core\Location;
use App\Http\Livewire\Component\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests;

    public $terminals = [];

    public $pageLoaded = false;

    public function mount()
    {
        
    }

    public function render()
    {
        return $this->renderView('livewire.pwa.order', [], 'layouts.pwa');
    }

    public function loadTerminals()
    {
        $location = Location::where('location', auth()->user()->office_location)->first();

        if(!empty($location->fortis_location_id)) {
            $fortis = new Fortis();

            $this->terminals = $fortis->fetchTerminals($location->fortis_location_id);
        }

        $this->pageLoaded = true;
    }
}
