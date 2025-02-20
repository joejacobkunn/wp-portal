<?php

namespace App\Http\Livewire\Component;

use Livewire\Component;
use App\Traits\Livewire\HasPlaceholder;

class Tabs extends Component
{
    use HasPlaceholder;

    /*
    |--------------------------------------------------------------------------
    | Configurable Attributes
    |--------------------------------------------------------------------------
    */

    /** Tab ID */
    public $tabId;

    /** Tab List */
    public $tabs = [];

    /** Custom Tab Headers */
    public $tabHeaders = [];

    /** Tab Content Definitions (may include livewire component details) */
    public $tabContents = [];

    /** Active tab info */
    public $activeTab;


    /**  Common content field */
    public $commonContent;

    public $parentComponent;


    /*
    |--------------------------------------------------------------------------
    | Non - Configurable Attributes
    |--------------------------------------------------------------------------
    */

    /** Active tab info (on component level) */
    public $componentActiveTab;

    public function mount()
    {
        $this->componentActiveTab = $this->activeTab;
    }

    public function render()
    {
        return view('livewire.component.tabs');
    }

    public function updateTab($tabId, $activeTab, $queryString = null)
    {
        $this->componentActiveTab = $activeTab;
        $this->dispatch('x-tab:changed', $tabId, $activeTab)->to($this->parentComponent);
    }
}
