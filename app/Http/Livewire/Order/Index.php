<?php

namespace App\Http\Livewire\Order;

use App\Http\Livewire\Component\Component;
use App\Models\SX\Company;
use App\Models\SX\Order;
use App\Traits\HasTabs;

class Index extends Component
{
    use HasTabs;

    public $account;

    public $orders = [];

    public $breadcrumbs = [
        [
            'title' => 'Orders',
        ],
    ];

    public $orderTab = 'orders';

    public $tabs = [
        'back-order-tabs' => [
            'active' => 'PendingReview',
            'links' => [
                'PendingReview' => 'Pending Review',
                'ignored' => 'Ignored',
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
        // $company = Company::find(40);
        // $company->state = 'MI';
        // $company->save();
        //dd($company);
        //dd($company->on('sx')->update(['state' => 'MI']));
        // $this->orders = Order::where('cono', 10)
        //     ->where('invoicedt', '=', '2022-03-30')
        //     ->where('whse', 'UTIC')
        //     ->get();
    }

    public function render()
    {
        return $this->renderView('livewire.order.index');
    }
}
