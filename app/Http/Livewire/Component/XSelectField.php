<?php

namespace App\Http\Livewire\Component;

use Exception;
use Livewire\Component;

class XSelectField extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    /**
     * Field ID
     */
    public $fieldId;

    /**
     * Dropdown Options
     */
    public $options = [];

    /**
     * Selected Options
     */
    public $selected = [];

    /**
     * Check if multiselect
     */
    public $multiple = false;
    
    /**
     * Dropdown placeholder text
     */
    public $placeholder = 'Please Select';

    /**
     * Show default option
     */
    public $defaultOption = true;

    /**
     * Default option label
     */
    public $defaultOptionLabel = 'Please Select';

    /**
     * Default option disabled
     */
    public $defaultOptionSelectable = false;


    /**
     * Disable select field
     */
    public $disabled = false;


    /**
     * Listener
     */
    public $listener = 'fieldUpdated';

    /**
     * @var String Label index in options provided
     */
    public $labelIndex = 'text';

    /**
     * @var String Value index in options provided
     */
    public $valueIndex = 'value';

    /**
     * @var String Value index in options provided
     */
    public $selectAllOption = true;

    /**
     * Hide search box
     */
    public $hideSearch = false;

    /**
     * Search field placeholder text
     */
    public $searchPlaceholder;

    /**
     * No results text
     */
    public $noResultText;

    /**
     * Dropdown direction
     */
    public $direction;

    /*
    |--------------------------------------------------------------------------
    | Non-Configurable Attributes 
    |--------------------------------------------------------------------------
    */

    /**
     * Field DOM ID
     */
    public $fieldDomId;
    
    /**
     * Rendered Options
     */
    public $items = [];

    /**
     * Rendered Selected Options
     */
    public $selectedItem = [];

    public function mount()
    {
        if ($this->options instanceof \Illuminate\Support\Collection) {
            $this->options = $this->options->toArray();
        }

        if (count($this->options) != count($this->options, COUNT_RECURSIVE)) {
            if (empty($this->labelIndex)) {
                throw new Exception('attribute: label-index required');
            }
            
            if (empty($this->valueIndex)) {
                throw new Exception('attribute: value-index required');
            }
        }

        //disable default select option selection on multiselect
        if ($this->multiple) {
            $this->defaultOptionSelectable = false;
        } else {
            $this->selectAllOption = false;
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        $this->fieldDomId = str_replace('.', '--', $this->fieldId) . '_' . uniqid();
        $this->items = $this->getItems();
        
        if ($this->selected) {
            $this->selectedItem = is_array($this->selected) ? $this->selected : [$this->selected];
        }

        return view('livewire.component.x-select-field');
    }

    /**
     * Get field options
     */
    public function getItems()
    {
        $items = [];

        //check if multidimentiional array
        if (count($this->options) == count($this->options, COUNT_RECURSIVE)) {
            foreach ($this->options as $item) {
                $items[] = [
                    'text' => $item,
                    'value' => $item,
                ];
            }
        } elseif (!empty($this->labelIndex) && !empty($this->valueIndex)) {
            foreach ($this->options as $item) {
                $items[] = [
                    'text' => $item[$this->labelIndex] ?? '',
                    'value' => $item[$this->valueIndex] ?? '',
                ];
            }
        }

        if (!$this->multiple && $this->defaultOption) {
            array_unshift($items, [
                'text' => $this->defaultOptionLabel,
                'value' => null,
                'disabled' => true,
                'default' => true,
            ]);
        }

        return $items;
    }
}
