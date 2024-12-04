<?php

namespace App\Http\Livewire\ServiceArea\Zones;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\ServiceArea\Zones\Traits\FormRequest;
use App\Models\ServiceArea\Zones;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, FormRequest;
    public  Zones $zone;
    public function render()
    {
        return view('livewire.service-area.zones.show');
    }

    public function submit()
    {

    }
}
