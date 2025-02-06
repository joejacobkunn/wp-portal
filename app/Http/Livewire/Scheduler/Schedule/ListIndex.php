<?php

namespace App\Http\Livewire\Scheduler\Schedule;

use App\Http\Livewire\Component\Component;
use App\Models\Scheduler\Schedule;
use App\Traits\HasTabs;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ListIndex extends Component
{
    use LivewireAlert, HasTabs;

    public $tabs = [
        'schedule-list-index-tabs' => [
            'active' => 'scheduled',
            'links' => [
                'scheduled' => 'Scheduled',
                'confirmed' => 'Confirmed',
                'unconfirmed' => 'Unconfirmed',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled',
            ],
        ]
    ];

    protected $queryString = [
        'tabs.schedule-list-index-tabs.active' => ['except' => '', 'as' => 'tab'],
    ];

    public function mount()
    {
        $this->authorize('viewAny', Schedule::class);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.schedule.list-index');
    }
}
