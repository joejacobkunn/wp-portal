<?php

namespace App\Traits;

trait HasTabs
{
    public $tabLoaded = false;

    public function processActiveTabChange($activeTab, $tabElement, $tabId)
    {
        $this->tabLoaded = false;
        $this->$tabElement = $activeTab;
        $this->emitSelf($tabId.':tab:changed', $activeTab);
        $this->dispatchBrowserEvent($tabId.':tabContentChanged');
    }

    public function initTabs()
    {
        $this->tabLoaded = true;
    }
}
