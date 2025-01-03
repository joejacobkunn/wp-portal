<?php

namespace App\Http\Livewire\SalesRepOverride;

use App\Http\Livewire\SalesRepOverride\Traits\SalesRepTrait;
use App\Models\SalesRepOverride\SalesRepOverride;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Livewire\Component\Component;
use App\Traits\HasTabs;

class Show extends Component
{
    use AuthorizesRequests, SalesRepTrait, HasTabs;
    public SalesRepOverride $salesRepOverride;
    public $editRecord =false;
    public $productLines;

    public $breadcrumbs = [
        [
            'title' => 'Sales Rep Override',
            'route_name' => 'sales-rep-override.index',

        ],
    ];
    public $tabs = [
        'sales-rep-comment-tabs' => [
            'active' => 'comments',
            'links' => [
                'comments' => 'Comments',
                'activity' => 'Activity',
            ],
        ],
    ];
    protected $listeners = [
        'deleteRecord' => 'delete',
        'edit' => 'edit',
        'closeUpdate' => 'closeUpdate',
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

    public function mount()
    {
        array_push($this->breadcrumbs, ['title' => $this->salesRepOverride->customer_number.'/'
            .$this->salesRepOverride->ship_to.'/'. $this->salesRepOverride->prod_line]);
        $this->formInit();
    }

    public function edit()
    {
        $this->editRecord=true;
    }

    public function closeUpdate()
    {
        $this->editRecord=false;
    }

    public function cancel()
    {
        $this->editRecord = false;
        $this->resetValidation();
        //$this->reset(['customerNumber', 'shipTo', 'prodLine', 'salesRep']);

    }

    public function render()
    {
        return $this->renderView('livewire.sales-rep-override.show');
    }
}
