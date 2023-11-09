<?php

namespace App\Http\Livewire\Core\Customer;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Livewire\Core\Customer\Traits\FormRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Index extends Component
{
    use AuthorizesRequests,FormRequest,LivewireAlert;

    public $account;

    public $addRecord = false;

    public $open_order_modal = false;

    public $open_order_details = [];

    public $lazyLoad = true;

    public $breadcrumbs = [
        [
            'title' => 'Customers',
        ],
    ];

    public $customer_types = [
        'HOM' => 'HOM',
        'LAN' => 'LAN',
        'SPC' => 'SPC',
        'EMP' => 'EMP',
        'WEB' => 'WEB',
        'MUN' => 'MUN',

    ];

    protected $listeners = ['showOpenOrder', 'closeModal'];

    public function mount()
    {
        $this->authorize('viewAny', Customer::class);

        $this->account = account();

        $this->formInit();
    }

    public function render()
    {
        return $this->renderView('livewire.core.customer.index');
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
