<?php

namespace App\Http\Livewire\Order;

use App\Http\Livewire\Component\Component;
use App\Models\Order\Order;
use App\Models\SX\Company;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Index extends Component
{
    use HasTabs, AuthorizesRequests;

    public $account;

    public $orders = [];

    public $statusCount = [];

    public $pendingReviewCount = 0;

    public $dnr_count = 0;

    public $pending_review_count = 0;

    public $follow_up_count = 0;

    public $ignored_count = 0;

    public $breadcrumbs = [
        [
            'title' => 'Orders',
        ],
    ];

    public function mount()
    {
        //$this->authorize('viewAny', DnrBackorder::class);


        $this->account = account();

    }

    public function render()
    {
        return $this->renderView('livewire.order.index');
    }

    public function filterDNROrders()
    {
        $this->dispatch('setFilter', 'is_dnr', '2');
    }

    public function getStatusCounts()
    {
        $this->dnr_count = Order::where('cono', auth()->user()->account->sx_company_number)->where('is_dnr', 1)->where('status', 'Pending Review')->count();
    }
}
