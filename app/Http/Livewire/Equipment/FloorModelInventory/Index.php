<?php

namespace App\Http\Livewire\Equipment\FloorModelInventory;

use App\Http\Livewire\Component\Component;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;

class Index extends Component
{
    use AuthorizesRequests, HasTabs;

    public $page;

    public $breadcrumbs = [
        [
            'title' => 'Floor Model Inventory',
        ],
    ];

    public $tabs = [
        'inventory-tabs' => [
            'active' => 'inventory',
            'links' => [
                'inventory' => 'Inventory',
                'notes' => 'Notes',
            ]
        ]
    ];

    protected $queryString = [
        'tabs.inventory-tabs.active' => ['except' => '', 'as' => 'tab'],
    ];

    public function mount()
    {
        $this->page ="Inventory List";
    }

    public function render()
    {
        return $this->renderView('livewire.equipment.floor-model-inventory.index');
    }

    #[On('floorModelInventory:updateSubHeading')]
    public function updateSubheading($subHeading)
    {
        $this->page = $subHeading;
    }
}
