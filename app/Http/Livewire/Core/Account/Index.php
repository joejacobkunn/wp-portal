<?php

namespace App\Http\Livewire\Core\Account;

use App\Http\Livewire\Component\Component;
use App\Http\Livewire\Core\Account\Traits\FormRequest;
use App\Models\Core\Account;
use App\Models\Core\SXAccount;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests, FormRequest;

    public $account;

    public $sx_accounts;

    public $addRecord = false;

    public $breadcrumbs = [
        [
            'title' => 'Accounts',
        ],
    ];

    public function render()
    {
        $this->authorize('viewAny', Account::class);

        return $this->renderView('livewire.core.account.index');
    }

    public function mount()
    {
        $this->account = new Account();
        $this->sx_accounts = SXAccount::all();
        $this->formInit();
    }

    public function create()
    {
        $this->authorize('store', Account::class);

        $this->addRecord = true;
    }

    /**
     * Form cancel action
     */
    public function cancel()
    {
        $this->formInit();
        $this->resetValidation();
        $this->resetExcept('account', 'sx_accounts');
    }
}
