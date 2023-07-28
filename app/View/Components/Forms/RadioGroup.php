<?php

namespace App\View\Components\Forms;

use Exception;
use Illuminate\View\Component;

class RadioGroup extends Component
{
    /**
     * @var String group label
     */
    public $label;
    
    /**
     * @var String Radio field name
     */
    public $name;

    /**
     * @var Array|Collection Options
     */
    public $items;

    /**
     * @var String parent component attribute
     */
    public $model;

    /**
     * @var String Label index in options provided
     */
    public $labelIndex;

    /**
     * @var String Value index in options provided
     */
    public $valueIndex;

    /**
     * @var Boolean show radio group in vertical
     */
    public $vertical;

    /**
     * @var String radio group grid
     */
    public $cols;

    public $renderedItems = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $items, $model, $label = null, $labelIndex = null, $valueIndex = null, $cols = 'col-md-3', $vertical = false)
    {
        $this->name = $name;
        $this->model = $model;
        $this->label = $label;
        $this->cols = $cols;
        $this->vertical = $vertical;
        if ($vertical) {
            $this->cols = 'col-md-12';
        }

        if ($items instanceof \Illuminate\Support\Collection) {
            $items = $items->toArray();
        }

        if (!is_array($items)) {
            throw new Exception('attribute: invalid items array');
        }

        if (array_keys($items) !== range(0, count($items) - 1)) {
            if (empty($labelIndex)) {
                throw new Exception('attribute: label-index required');
            }
            
            if (empty($valueIndex)) {
                throw new Exception('attribute: value-index required');
            }
        }

        if (!empty($labelIndex) && !empty($valueIndex)) {
            foreach ($items as $item) {
                $this->renderedItems[] = [
                    'label' => $item[$labelIndex] ?? '',
                    'value' => $item[$valueIndex] ?? '',
                ];
            }
        } elseif (array_keys($items) === range(0, count($items) - 1)) {
            //if items are sequential
            foreach ($items as $item) {
                $this->renderedItems[] = [
                    'label' => $item,
                    'value' => $item,
                ];
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.forms.radio-group');
    }
}
