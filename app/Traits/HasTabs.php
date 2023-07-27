<?php

namespace App\Traits;

trait HasTabs
{
    public function processActiveTabChange($activeTab, $tabElement, $tabId)
    {
        $this->$tabElement = $activeTab;
        $this->emitSelf($tabId . ':tab:changed', $activeTab);
        $this->dispatchBrowserEvent($tabId . ':tabContentChanged');
    }
}
