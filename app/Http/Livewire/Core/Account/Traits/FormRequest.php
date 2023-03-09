<?php

namespace App\Http\Livewire\Core\Account\Traits;

use App\Models\Core\Role;
use App\Models\Core\User;
use App\Models\Core\Account;
use App\Events\User\UserCreated;

trait FormRequest
{
    public $adminEmail;

    public $adminUser;

    public $accountMetaData;

    protected $validationAttributes = [
        'account.name' => 'Name',
        'account.subdomain' => 'Subdomain',
    ];

    protected function rules()
    {
        return [
            'account.name' => 'required|min:3',
            'account.subdomain' => 'required|min:3|unique:accounts,subdomain' . ($this->account ? ',' . $this->account->id : ''),
            'account.is_active' => 'nullable',
        ];
    }

    /**
     * Initialize form attributes
     */ 
    public function formInit()
    {
        if (empty($this->account->id)) {
            $this->account = new Account;
            $this->account->name = null;
            $this->account->subdomain = null;
            $this->account->admin_user = null;
        } else {
            $this->adminUser = $this->account->admin()->first();
            $this->adminEmail = $this->adminUser?->email;
            $this->accountMetaData = $this->account?->metadata()->first();
        }
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (!empty($this->account->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new account
     */
    public function store()
    {
        $this->authorize('store', Account::class);

        $this->account->is_active = 1;
        $this->account->save();
        $this->setAdminUser();

        session()->flash('success', 'Account created!');
        return redirect()->route('core.account.show', [
            'account' => $this->account->id
        ]);
    }

    /**
     * Update existing account
     */
    public function update()
    {
        $this->authorize('update', $this->account);

        $this->account->save();
        $this->setAdminUser();

        $this->editRecord = false;
        session()->flash('success', 'Account updated!');
    }


    public function setAdminUser()
    {
        $user = User::where('account_id', $this->account->id)
            ->where('email', $this->adminEmail)
            ->firstOrNew();

        if (empty($user->id)) {
            $user->email = $this->adminEmail;
            $user->password = uniqid();
            $user->name = "";
            $user->is_active = User::ACTIVE;
            $user->account_id = $this->account->id;
            $user->save();

            $user->metadata()->create([
                'invited_by' => auth()->user()->id,
            ]);

            //send notifications
            UserCreated::dispatch($user);
        } elseif ($user->account_id != $this->account->id) {
            $user->account_id = $this->account->id;
            $user->save();
        }

        if ($this->adminUser?->id != $user?->id) {

            if ($this->adminUser) {
                //update existing role
                $this->adminUser->roles()->detach();
                $this->adminUser->assignRole(Role::USER_ROLE);
            }

            $this->account->admin_user = $user->id;
            $this->account->save();
            $user->roles()->detach();
            $user->assignRole(Role::SUPER_ADMIN_ROLE);
        }
    }
}
