<?php

namespace App\Http\Livewire\Order\NotificationTemplate;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Order\NotificationTemplate\Traits\FormRequest;
use App\Models\Order\NotificationTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public NotificationTemplate $template;

    public $addRecord = false;

    public $breadcrumbs = [
        [
            'title' => 'Orders',
            'route_name' => 'order.index',
        ],
        [
            'title' => 'Notification Templates',
            'route_name' => 'order.email-template.index',
        ],
    ];

    public function mount()
    {
        //$this->authorize('viewAny', NotificationTemplate::class);

        $this->formInit();
    }

    public function render()
    {
        return $this->renderView('livewire.order.email_template.index');
    }

    public function create()
    {
        //$this->authorize('store', NotificationTemplate::class);

        $this->addRecord = true;
    }

    /**
     * Form cancel action
     */
    public function cancel()
    {
        $this->formInit();
        $this->resetValidation();
    }
}
