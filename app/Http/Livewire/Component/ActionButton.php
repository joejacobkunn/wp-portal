<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class ActionButton extends Component
{
    public bool $loadData = false;

    public function init()
    {
        $this->loadData = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Configurable Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * Action button list
     */
    public $actionButtons = [];

     /*
    |--------------------------------------------------------------------------
    | Non-Configurable Attributes
    |--------------------------------------------------------------------------
    */

    /** Action confirm required flag */
    public $actionConfirm = false;

    /** Last button clicked  */
    public $activeButton;

    /** Render component view */
    public function render()
    {
        return view('livewire.component.action-button');
    }

    /**
     * Button click event listener (pre confirmation check)
     * */
    public function buttonClicked($button)
    {
        $listener = $button['listener'] ?? '';
        if (! empty($button['confirm'])) {
            $this->activeButton = $button;
            $this->actionConfirm = true;
        } else {
            $this->emit($listener);
        }
    }

    /**
     * Confirm button listener (applicable if action confirmation required)
     * */
    public function actionConfirmed()
    {
        $this->actionConfirm = false;
        $this->emit($this->activeButton['listener']);
    }

    /**
     * Cancel button listener (applicable if action confirmation required)
     * */
    public function actionCancel()
    {
        $this->actionConfirm = false;
        $this->activeButton = null;
    }
}
