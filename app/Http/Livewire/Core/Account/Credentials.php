<?php

namespace App\Http\Livewire\Core\Account;

use App\Http\Livewire\Component\Component;
use App\Models\Core\Account;
use App\Traits\HasTabs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Credentials extends Component
{
    use AuthorizesRequests, HasTabs;

    public Account $account;

    protected $listeners = [
        'closeModal' => 'closeKeyPopup',
    ];

    public $keys = [];

    public $generateKeyModal = false;

    public $keysLoaded = false;

    public $keyLabel = null;

    public function render()
    {
        return view('livewire.core.account.credentials');
    }

    public function mount()
    {

    }

    public function loadKeys()
    {
        $this->keysLoaded = true;
        $this->keys = $this->account
            ->apiKeys()
            ->limit(20)
            ->latest()
            ->get();
    }

    public function openGenerateKeyModal()
    {
        $this->keyLabel = '';
        $this->generateKeyModal = true;
    }

    public function generateKey()
    {
        $validatedData = $this->validate(
            ['keyLabel' => 'required'],
            [
                'keyLabel.required' => 'The :attribute cannot be empty.',
            ],
            ['keyLabel' => 'Label']
        );

        $keyDetails = $this->account->createKey($this->keyLabel);

        $this->dispatchBrowserEvent('account:key-generated', $keyDetails);
        $this->loadKeys();
    }

    public function revokeAccess($key)
    {
        $this->account->revokeKey($key);
        $this->sendAlert('success', 'Access Key Revoked!');
        $this->loadKeys();
    }

    public function closeKeyPopup()
    {
        $this->generateKeyModal = false;
    }
}
