<?php

namespace App\Http\Livewire\Scheduler\Drivers;

use App\Http\Livewire\Component\Component;

class Index extends Component
{

    public $breadcrumbs = [
        [
            'title' => 'Drivers',
            'route_name' => 'schedule.driver.index'
        ],
    ];

    public function render()
    {
        return $this->renderView('livewire.scheduler.drivers.index');
    }
}
