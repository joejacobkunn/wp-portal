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
     * @var Integer Editor Height
     */
    public $height = 150; //in px

    /**
     * @var Integer Editor Max Height
     */
    public $maxHeight = 450; //in px

    /**
     * @var Integer Editor Max Height
     */
    public $maxLength;

    public $placeholder;

    public $showCharCount = false;

    //count html chars as well
    public $strictCount = false;

    /**
     * Listener
     */
    public $listener = 'fieldUpdated';

    public $parentComponent;

    public function render()
    {
        return view('livewire.component.html-editor');
    }

    public function setValue($value)
    {
        $this->dispatch($this->listener, $this->model, $value)->to($this->parentComponent);
    }
}
