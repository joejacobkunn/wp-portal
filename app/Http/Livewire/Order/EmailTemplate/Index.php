<?php

namespace App\Http\Livewire\Order\EmailTemplate;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Order\EmailTemplate\Traits\FormRequest;
use App\Models\Order\EmailTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public EmailTemplate $template;

    public $addRecord = false;

    public $breadcrumbs = [
        [
            'title' => 'Orders',
            'route_name' => 'order.index',
        ],
        [
            'title' => 'Email Templates',
            'route_name' => 'order.email-template.index',
        ],
    ];

    public function mount()
    {
        //$this->authorize('viewAny', EmailTemplate::class);

        $this->formInit();
    }

    public function render()
    {
        return $this->renderView('livewire.order.email_template.index');
    }

    public function create()
    {
        //$this->authorize('store', EmailTemplate::class);

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
