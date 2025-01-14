<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class DatePicker extends Component
{
    /** @var string parent model attribute */
    public $model;

    /** @var string Initial value */
    public $value;

    /** @var string Date format */
    public $format;

    /** @var string input field icon */
    public $icon;

    /** @var string placeholder */
    public $placeholder;

    /** @var string Min Date */
    public $minDate;

    /** @var string Max Date */
    public $maxDate;

    /** @var boolean Enable time picker */
    public $enableTime;

    /** @var boolean Disable datepicker */
    public $disabled;

    /** @var boolean Make field readonly */
    public $readonly;

    /** @var boolean Hide input icon */
    public $hideIcon;

    /** @var string Datepicker type (datepicker | timepicker) */
    public $type = 'datepicker';

    /** @var boolean Show twelve hour clock */
    public $twelveHourClock = false;

    /** @var boolean Clearable input field */
    public $clearable = false;

    /** @var string field update listener */
    public $listener = 'fieldUpdated';

    public $parentComponent;
    
    public function mount()
    {
        $this->readonly = $this->disabled ? false : $this->readonly;
    }

    public function render()
    {
        return view('livewire.component.datepicker');
    }

    public function updatedValue()
    {
        $this->dispatch($this->listener, $this->model, $this->value)->to($this->parentComponent);
    }
}
