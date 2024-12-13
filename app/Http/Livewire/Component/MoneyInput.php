<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class MoneyInput extends Component
{
    /** @var string parent model attribute */
    public $model;

    /** @var string Initial value */
    public $value;

    /** @var string input field icon */
    public $icon;

    /** @var string placeholder */
    public $placeholder;

    /** @var boolean Hide input icon */
    public $hideIcon;

    /** @var boolean Keep format */
    public $keepFormat;

    /** @var boolean Keep format */
    public $lazy;

    /** @var boolean Disable datepicker */
    public $disabled;

    /** @var int Fraction limit */
    public $fractionDigits;

    /** @var string field update listener */
    public $listener = 'fieldUpdated';

    public $parentComponent;
    
    public function mount()
    {
        
    }

    public function render()
    {
        return view('livewire.component.money-input');
    }

    public function updateValueInput($value)
    {
        $this->dispatch($this->listener, $this->model, $value)->to($this->parentComponent);
    }
}
