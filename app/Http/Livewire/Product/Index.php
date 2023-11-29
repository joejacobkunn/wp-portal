<?php

namespace App\Http\Livewire\Product;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests;

    public $account;

    public $addRecord = false;

    public $open_order_modal = false;

    public $open_order_details = [];

    public $lazyLoad = true;

    public $breadcrumbs = [
        [
            'title' => 'Products',
        ],
    ];

    protected $listeners = [
        'showOpenOrder',
        'closeModal',
        'customer:form:cancel' => 'cancel'
    ];

    public function mount()
    {
        //$this->authorize('viewAny', Customer::class);

        $this->account = account();
    }

    public function render()
    {
        return $this->renderView('livewire.product.index');
    }

    public function create()
    {
        $this->addRecord = true;
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
