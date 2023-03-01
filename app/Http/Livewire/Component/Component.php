<?php

namespace App\Http\Livewire\Component;

use Livewire\Component as BaseComponent;

abstract class Component extends BaseComponent
{
    
    public $breadcrumbs = [];

    public $actionButtons = [];

    public $moduleName;

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
                if (is_object($updatingObj)) {
                    $updatingObj = &$updatingObj->{$fieldAttribute};
                } elseif (is_array($updatingObj)) {
                    $updatingObj = &$updatingObj[$fieldAttribute];
                }
            }

            if (is_object($updatingObj)) {
                $updatingObj->{$updatingAttr} = $value;
            } elseif (is_array($updatingObj)) {
                $updatingObj[$updatingAttr] = $value;
            }
        } else {
            $this->{$name} = $value;
        }

        if ($recheckValidation && isset($this->getRules()[$name])) {
            $this->validateOnly($name);
        }
    }

    public function renderView($viewPath, $args = [], $layoutView = 'livewire-app')
    {
        return view($viewPath, $args)->extends($layoutView, ['moduleName' => $this->moduleName]);
    }
}