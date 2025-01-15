<?php

namespace App\Http\Livewire\Scheduler\Truck;

use App\Models\Scheduler\Truck;
use App\Http\Livewire\Component\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, LivewireAlert;
    
    public Truck $truck;

    public function mount()
    {
        $this->authorize('view', $this->truck);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.truck.show');
    }
}
