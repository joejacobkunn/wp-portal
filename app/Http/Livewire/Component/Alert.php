<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class Alert extends Component
{
    /**
    * The priority of the alert, i.e., "success", "info", "danger", or "warning"
    *
    * @var string
    */
    public $level;

    /**
    * The message presenting to the user
    *
    * @var string
    */
    public $message;

    /**
     * Message Icon presenting to the user
     */
    public $messageIcon;

    /**
     * Alert has Action
     */
    public $hasAction;

    /**
     * Alert Action Button Name
     */
    public $actionButtonName = 'Click';

    /**
     * Alert Action Button Class
     */
    public $actionButtonClass = 'btn-outline-primary';

    /**
     * Alert Action Button Action
     */
    public $actionButtonAction;

    public function render()
    {
        return view('livewire.component.alert');
    }

    public function callAction()
    {
        $this->dispatch($this->actionButtonAction);
    }
}
