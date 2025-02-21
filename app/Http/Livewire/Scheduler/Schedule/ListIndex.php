<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use Carbon\Carbon;
use App\Traits\HasTabs;
use App\Models\Core\Warehouse;
use App\Models\Scheduler\Schedule;
use App\Http\Livewire\Component\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Http\Livewire\Scheduler\Schedule\Traits\ScheduleData;
use App\Models\Scheduler\Truck;

class ListIndex extends Component
{
    use LivewireAlert, HasTabs, ScheduleData;

    public $showEventModal;
    public $selectedSchedule;
    public $scheduleId;
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
        'scheduleId' => ['except' => '*', 'as' => 'id'],
    ];

    protected $listeners = [
        'schedule-list-index-tabs:tab:changed' => 'indexActiveTabChange',
        'schedule-event-modal-open' => 'scheduleModalOpen',
        'closeEventModal' => 'closeEventModal'
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

        if($this->scheduleId) {
            $this->scheduleModalOpen($this->scheduleId);
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

    public function getTrucksProperty()
    {
        return Truck::select('id', 'truck_name', 'whse')->limit(100)->get();
    }

    public function indexActiveTabChange($activeTab)
    {
        $this->updateTabCounts();
    }

    public function updateTabCounts()
    {
        $this->tabCounts['today'] = $this->queryByDate(Carbon::now()->toDateString())->where('schedules.whse', $this->activeWarehouse->short)->count();
        $this->tabCounts['tomorrow'] = $this->queryByDate(Carbon::now()->addDay()->toDateString())->where('schedules.whse', $this->activeWarehouse->short)->count();
        $this->tabCounts['unconfirmed'] = $this->queryByStatus('unconfirmed')->where('schedules.whse', $this->activeWarehouse->short)->count();
        $this->tabCounts['all'] = $this->scheduleBaseQuery()->where('schedules.whse', $this->activeWarehouse->short)->count();
    }

    public function scheduleModalOpen($id)
    {
        $this->selectedSchedule = Schedule::find($id);
        $this->scheduleId =  $this->selectedSchedule->id;
        $this->showEventModal = true;
    }

    public function closeEventModal()
    {
        $this->reset(['selectedSchedule', 'scheduleId']);
        $this->showEventModal = false;
    }
}
