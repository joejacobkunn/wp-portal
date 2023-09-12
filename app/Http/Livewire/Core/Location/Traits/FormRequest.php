<?php

namespace App\Http\Livewire\Core\Location\Traits;

use App\Events\User\UserCreated;
use App\Models\Core\Account;
use App\Models\Core\Location;
use App\Models\Core\Role;
use App\Models\Core\User;

trait FormRequest
{
    protected $validationAttributes = [
        'location.name' => 'Location Name',
        'location.phone' => 'Phone',
        'location.address' => 'Address',
    ];

    protected function rules()
    {
        return [
            'location.name' => 'required|min:3',
            'location.phone' => 'required',
            'location.address' => 'required',
            'location.is_active' => 'required|boolean',
        ];
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        $this->location = new Location();
        $this->location->name = null;
        $this->location->phone = null;
        $this->location->address = null;
        $this->location->is_active = true;
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (! empty($this->location->id)) {
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
        $this->authorize('store', Location::class);

        $this->location->account_id = $this->account->id;

        $this->location->save();

        $this->addLocation = false;
        $this->formInit();
        $this->resetValidation();

        session()->flash('success', 'Location created!');

    }

    /**
     * Update existing account
     */
    public function update()
    {
        $this->authorize('update', $this->location);

        $this->location->save();
        $this->setAdminUser();

        $this->editRecord = false;
        session()->flash('success', 'Account updated!');
    }

    public function setAdminUser()
    {
        $user = User::where('account_id', $this->location->id)
            ->where('email', $this->adminEmail)
            ->firstOrNew();

        if (empty($user->id)) {
            $user->email = $this->adminEmail;
            $user->name = '';
            $user->is_active = User::ACTIVE;
            $user->account_id = $this->location->id;
            $user->save();

            $user->metadata()->create([
                'invited_by' => auth()->user()->id,
            ]);

            //send notifications
            UserCreated::dispatch($user);
        } elseif ($user->account_id != $this->location->id) {
            $user->account_id = $this->location->id;
            $user->save();
        }

        if ($this->adminUser?->id != $user?->id) {

            if ($this->adminUser) {
                //update existing role
                $this->adminUser->roles()->detach();
                $this->adminUser->assignRole(Role::USER_ROLE);
            }

            $this->location->admin_user = $user->id;
            $this->location->save();
            $user->roles()->detach();
            $user->assignRole(Role::SUPER_ADMIN_ROLE);
        }
    }
}
