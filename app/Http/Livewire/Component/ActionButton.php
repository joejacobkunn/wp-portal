<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class ActionButton extends Component
{

    /*
    |--------------------------------------------------------------------------
    | Configurable Attributes 
    |--------------------------------------------------------------------------
    */
    
    /**
     * Action button list
     */
    public $actionButtons = [];

    /**
     * Button group Class
     */
    public $btnGroupClass = '';

     /*
    |--------------------------------------------------------------------------
    | Non-Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    /** Action confirm required flag */
    public $actionConfirm = false;

    /** Last button clicked  */
    public $activeButton;

    protected $listeners = [
        'closeModal' => 'closeModal',
    ];

    /** Render component view */
    public function render()
    {
        return view('livewire.component.action-button');
    }

    /** 
     * Button click event listener (pre confirmation check)
     * */
    public function buttonClicked($actionButtonIndex) {
        $listener = $this->actionButtons[$actionButtonIndex]['listener'] ?? "";
        if (!empty($this->actionButtons[$actionButtonIndex]['confirm'])) {
            $this->activeButton = $this->actionButtons[$actionButtonIndex];
            $this->actionConfirm = true;
        } else {
            $this->dispatch($listener);
        }
    }

    /** 
     * Confirm button listener (applicable if action confirmation required)
     * */
    public function actionConfirmed() {
        $this->actionConfirm = false;
        $this->dispatch($this->activeButton['listener']);
    }

    /** 
     * Cancel button listener (applicable if action confirmation required)
     * */
    public function actionCancel() {
        $this->actionConfirm = false;
        $this->activeButton = null;
    }

    /** 
     * Close button (X) listener for modal
     * */
    public function closeModal() {
        $this->actionConfirm = false;
        $this->activeButton = null;
    }
    
}
