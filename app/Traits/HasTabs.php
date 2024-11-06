<?php

namespace App\Traits;

trait HasTabs
{
    public $hasTabs = true;

    public function processActiveTabChange($tabId, $activeTab, $queryString = null)
    {
        $this->tabs[$tabId]['active'] = $activeTab;
        $this->dispatch($tabId . ':tab:changed', $activeTab);
    }
}
