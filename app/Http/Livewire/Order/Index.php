<?php

namespace App\Http\Livewire\Order;

use App\Http\Livewire\Component\Component;
use App\Models\Order\DnrBackorder;
use App\Models\SX\Company;
use App\Models\SX\Order;
use App\Traits\HasTabs;

class Index extends Component
{
    use HasTabs;

    public $account;

    public $orders = [];

    public $pendingReviewCount = 0;

    public $breadcrumbs = [
        [
            'title' => 'Orders',
        ],
    ];

    public $orderTab = 'back_orders';

    public $tabs = [
        'back-order-tabs' => [
            'active' => 'PendingReview',
            'links' => [
                'PendingReview' => 'Pending Review',
                'ignored' => 'Ignored',
                'follow_up' => 'Follow Up',
                'cancelled' => 'Cancelled',
                'errors' => 'Errors',
                'Closed' => 'Closed',
            ],
        ]
    ];

    protected $queryString = [
        'orderTab' => ['except' => '', 'as' => 'tab'],
    ];

    public function mount()
    {
        $this->account = account();
        $this->pendingReviewCount = DnrBackorder::where('status', 'Pending Review')->count();

    }

    public function render()
    {
        return $this->renderView('livewire.order.index');
    }
}
