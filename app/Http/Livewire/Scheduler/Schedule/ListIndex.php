<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Schedule;
use App\Traits\HasTabs;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ListIndex extends Component
{
    use LivewireAlert, HasTabs;

    public $tabs = [
        'schedule-list-index-tabs' => [
            'active' => 'today',
            'links' => [
                'today' => 'Today',
                'tomorrow' => 'Tomorrow',
                'confirmed' => 'Confirmed',
                'all' => 'All',
            ],
        ]
    ];

    protected $queryString = [
        'tabs.schedule-list-index-tabs.active' => ['except' => '', 'as' => 'tab'],
        'activeWarehouseId' => ['except' => '', 'as' => 'whse'],
    ];

    public $activeWarehouseId;

    public function getWarehousesProperty()
    {
        $data = Warehouse::select(['id', 'short', 'title'])
            ->where('cono', 10)
            ->orderBy('title', 'asc')
            ->get();

        return $data;
    }

    public function getActiveWarehouseProperty()
    {
        return $this->warehouses->find($this->activeWarehouseId);
    }

    public function mount()
    {
        $this->authorize('viewAny', Schedule::class);

        if (empty($this->activeWarehouseId)) {
            $this->activeWarehouseId = $this->warehouses->firstWhere('title', auth()->user()->office_location)?->id;
        }
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.schedule.list-index');
    }

    public function changeWarehouse($wsheID)
    {
        $this->activeWarehouseId = $wsheID;
    }
}
