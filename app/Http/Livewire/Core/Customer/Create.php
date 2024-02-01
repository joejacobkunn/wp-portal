<?php

namespace App\Http\Livewire\Core\Customer;

use App\Http\Livewire\Component\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Livewire\Core\Customer\Traits\FormRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Create extends Component
{
    use AuthorizesRequests,FormRequest,LivewireAlert;

    public $account;

    public $sourcePopup = false;

    public $customer_types = [
        'HOM' => 'HOM',
        'LAN' => 'LAN',
        'SPC' => 'SPC',
        'EMP' => 'EMP',
        'WEB' => 'WEB',
        'MUN' => 'MUN',
    ];

    public function mount()
    {
        $this->account = account();
        $this->formInit();
    }

    public function render()
    {
        return $this->renderView('livewire.core.customer.create');
    }

    public function cancel()
    {
        $this->dispatch('customer:form:cancel');
    }
}
