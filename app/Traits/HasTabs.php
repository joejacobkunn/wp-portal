<?php

namespace App\Traits;

trait HasTabs
{
    public $hasTabs = true;

    public function processActiveTabChange($activeTab, $tabElement, $tabId, $queryString = null)
    {
        $this->tabs[$tabElement]['active'] = $activeTab;
        $this->dispatch($tabId . ':tab:changed', $activeTab)->self();
        $this->dispatch($tabId . ':tabContentChanged');
    }
}
