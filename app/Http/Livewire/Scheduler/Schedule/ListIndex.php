<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use Carbon\Carbon;
use App\Traits\HasTabs;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Schedule;
use App\Http\Livewire\Component\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Http\Livewire\Scheduler\Schedule\Traits\ScheduleData;

class ListIndex extends Component
{
    use LivewireAlert, HasTabs, ScheduleData;

    public $tabs = [
        'schedule-list-index-tabs' => [
            'active' => 'today',
            'links' => [
                'today' => 'Today',
                'tomorrow' => 'Tomorrow',
                'unconfirmed' => 'Unconfirmed',
                'all' => 'All',
            ],
        ]
    ];

    protected $queryString = [
        'tabs.schedule-list-index-tabs.active' => ['except' => '', 'as' => 'tab'],
        'activeWarehouseId' => ['except' => '', 'as' => 'whse'],
    ];

    protected $listeners = [
        'schedule-list-index-tabs:tab:changed' => 'indexActiveTabChange',
    ];

    public $tabCounts = [];

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
        $this->updateTabCounts();
        return $this->renderView('livewire.scheduler.schedule.list-index');
    }

    public function changeWarehouse($wsheID)
    {
        $this->activeWarehouseId = $wsheID;
    }

    public function indexActiveTabChange($activeTab)
    {
        $this->updateTabCounts();
    }

    public function updateTabCounts()
    {
        $this->tabCounts['today'] = $this->queryByDate(Carbon::now()->toDateString())->where('orders.whse', $this->activeWarehouseId)->count();
        $this->tabCounts['tomorrow'] = $this->queryByDate(Carbon::now()->addDay()->toDateString())->where('orders.whse', $this->activeWarehouseId)->count();
        $this->tabCounts['unconfirmed'] = $this->queryByStatus('unconfirmed')->where('orders.whse', $this->activeWarehouseId)->count();
        $this->tabCounts['all'] = $this->scheduleBaseQuery()->count();
    }
}
