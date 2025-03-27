<?php

namespace App\Http\Livewire\Scheduler\NotificationTemplate;

use App\Classes\OpenAI;
use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Scheduler\NotificationTemplate\Form\NotificationForm;
use App\Models\Scheduler\NotificationTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests;

    public NotificationTemplate $template;
    public NotificationForm $form;

    public $breadcrumbs = [
        [
            'title' => 'Scheduler',
        ],
        [
            'title' => 'Notification Templates',
            'route_name' => 'schedule.email-template.index',
        ],
    ];

    public function mount()
    {        
        $this->authorize('viewAny', NotificationTemplate::class);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.notification-template.index');
    }

}
