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

    public $statusCount = [];

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

        $this->statusCount = [
            'pendingReviewCount' => DnrBackorder::where('status', 'Pending Review')->count(),
            'ignoredCount' => DnrBackorder::where('status', 'Ignored')->count(),
            'followUpCount' => DnrBackorder::where('status', 'Follow Up')->count(),
            'cancelledCount' => DnrBackorder::where('status', 'Cancelled')->count(),
            'errorsCount' => DnrBackorder::where('status', 'Error')->count()
        ];

    }

    public function render()
    {
        return $this->renderView('livewire.order.index');
    }

    public function updateExistingOrders()
    {
        $pending_orders = DnrBackorder::where('status', 'Pending Review')->get();

        foreach($pending_orders as $pending_order)
        {
            $stage_code = Order::select('stagecd')->where('cono', auth()->user()->account->sx_company_number)->where('orderno', $pending_order->order_number)->where('ordersuf', $pending_order->order_number_suffix)->first()->stagecd;
            
            if($stage_code > 2)
            {
                if(in_array($stage_code, [3,4,5])) $status = 'Closed';
                if(in_array($stage_code, [9])) $status = 'Cancelled';
                $pending_order->update(['status' => $status, 'stage_code' => $stage_code]);
            }

        }
    }
}
