<?php

namespace App\Http\Livewire\Order\NotificationTemplate;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Order\NotificationTemplate\Traits\FormRequest;
use App\Models\Order\NotificationTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests, FormRequest;

    //Attributes
    public NotificationTemplate $template;

    public $breadcrumbs = [
        [
            'title' => 'Orders',
            'route_name' => 'order.index',
        ],
        [
            'title' => 'Email Templates',
            'route_name' => 'order.email-template.index',
        ]
    ];

    public $actionButtons = [
        [
            'icon' => 'fa-edit',
            'color' => 'primary',
            'listener' => 'edit',
        ],
        [
            'icon' => 'fa-trash',
            'color' => 'danger',
            'confirm' => true,
            'confirm_header' => 'Confirm Delete',
            'listener' => 'deleteRecord',
        ],
    ];

    protected $listeners = [
        'deleteRecord' => 'delete',
        'edit' => 'edit',
        'updateStatus' => 'updateStatus'
    ];

    public $editRecord = false;

    public function mount()
    {
        $this->formInit();

        array_push($this->breadcrumbs, ['title' => $this->template->name]);
    }

    public function render()
    {
        return $this->renderView('livewire.order.email_template.show');
    }

    public function edit()
    {
        $this->editRecord = true;
    }

    public function delete()
    {
        //$this->authorize('delete', $this->template);
        $this->template->delete();
        session()->flash('success', 'Template Deleted !');

        return $this->redirect(route('order.email-template.index'), navigate: true);
    }

    public function cancel()
    {
        //reset dirty attributes to original
        $this->template->refresh();
        $this->formInit();
        $this->resetValidation();
        $this->reset(['editRecord']);
    }

    public function activate()
    {
        $this->template->is_active = 1;
        $this->template->save();
    }

    public function deactivate()
    {
        $this->template->is_active = 0;
        $this->template->save();
    }

}
