<?php

namespace App\Http\Livewire\Component;

use Livewire\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    public $breadcrumbs = [];

    public $actionButtons = [];

    /**
     * Dynamic listener definitions
     */
    public function getListeners()
    {
        $listeners = $this->listeners;
        
        $listeners = array_merge($listeners, [
            'fieldUpdated' => 'fieldUpdated',
        ]);
        
        return $listeners;
    }
    
    /**
     * Set attribute values
     */
    public function fieldUpdated($name, $value, $recheckValidation = true)
    {
        if (str_contains($name, '.')) {
            $fieldAttributes = explode('.', $name,);
            $updatingAttr = array_pop($fieldAttributes);
            $updatingObj = &$this;
            foreach ($fieldAttributes as $fieldAttribute) {
                $updatingObj = &$updatingObj->{$fieldAttribute};
            }

            $updatingObj->{$updatingAttr} = $value;
        } else {
            $this->{$name} = $value;
        }

        if ($recheckValidation && isset($this->getRules()[$name])) {
            $this->validateOnly($name);
        }
    }
}