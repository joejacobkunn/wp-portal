<?php

namespace App\Http\Livewire\Core\Customer;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Customer;
use App\Models\Order\Order;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, HasTabs;

    public $account;

    public $addRecord = false;

    public $open_order_modal = false;

    public $open_order_details = [];

    public $takenByMeCount = 0;

    public $breadcrumbs = [
        [
            'title' => 'Customers',
        ],
    ];

    public $tabs = [
        'customer-tabs' => [
            'active' => 'all',
            'links' => [
                'all' => 'All customers',
                'my_customers' => 'Taken by Me',
            ],
        ]
    ];

    protected $listeners = [
        'showOpenOrder',
        'closeModal',
        'customer:form:cancel' => 'cancel'
    ];

    public function mount()
    {
        $this->authorize('viewAny', Customer::class);

        $this->account = account();

        $this->takenByMeCount = Order::where('taken_by', auth()->user()->sx_operator_id)->whereNotIn('stage_code', [4,5,9])->count();
    }

    public function render()
    {
        return $this->renderView('livewire.core.customer.index');
    }

    public function countTakenBys()
    {
        $this->takenByMeCount = Order::where('taken_by', auth()->user()->sx_operator_id)->whereNotIn('stage_code', [4,5,9])->count();
    }

    public function create()
    {
        $this->addRecord = true;
    }

    public function showOpenOrder($customer_id)
    {
        $customer = Customer::find($customer_id);
        $this->open_order_details = $customer->open_order_details;
        $this->open_order_modal = true;
    }

    public function closeModal()
    {
        $this->open_order_modal = false;
    }

    public function cancel()
    {
        $this->addRecord = false;
    }
}
