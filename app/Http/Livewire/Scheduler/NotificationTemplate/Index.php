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
        $openai = new OpenAI();
        echo '<img src="'.json_decode($openai->generateImage('generate a picture of a Gooseneck Trailer with cargo dimensions of 30 ft x7.5 ft x20 ft. Make sure all text are in english', '1024x1024'))->data[0]->url.'" />';
        exit;
        
        $this->authorize('viewAny', NotificationTemplate::class);
    }

    public function render()
    {
        return $this->renderView('livewire.scheduler.notification-template.index');
    }

}
