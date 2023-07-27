<?php

namespace App\Traits\Livewire;

use Livewire\Event;
use Livewire\Livewire;

trait ReceivesEvents
{
    public function fireEvent($event, $params, $id)
    {
        $method = $this->getEventsAndHandlers()[$event];

        $this->callMethod($method, $params, function ($returned) use ($event, $id, $params) {
            Livewire::dispatch('action.returned', $this, $event, $returned, $id);

            if (in_array('browser:callback', $params)) {
                $this->dispatchBrowserEvent($event.':emit');
            }
        });
    }
}
