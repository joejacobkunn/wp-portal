<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;

class HtmlEditor extends Component
{
    /**
     * @var string Field label
     */
    public $label;

    /**
     * @var string parent component attribute
     */
    public $model;

    /**
     * @var string Initial value
     */
    public $value;

    /**
     * @var string Field ID
     */
    public $fieldId;

    /**
     * @var int Editor Height
     */
    public $height = 250; //in px

    /**
     * @var int Editor Max Height
     */
    public $maxHeight = 450; //in px

    public function render()
    {
        return view('livewire.component.html-editor');
    }
}
