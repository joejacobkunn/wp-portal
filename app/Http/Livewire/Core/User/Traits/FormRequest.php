<?php

namespace App\Http\Livewire\Core\User\Traits;

use App\Enums\User\UserStatusEnum;
use App\Models\Core\Role;
use App\Models\Core\User;
use App\Events\User\UserCreated;

trait FormRequest
{
    protected $validationAttributes = [
        'user.name' => 'Name',
        'user.email' => 'Email',
    ];

    public $roles;
    public $selectedRole;
    public $userEmail;

    protected function rules()
    {
        $rules = [
            'user.name' => 'required',
        ];

        if(auth()->user()->can('manageRole', auth()->user())) {
            $rules['selectedRole'] = 'required';
        }

        return $rules;
    }

    /** Properties */
    public function getStatusAlertClassProperty()
    {
        return $this->user->is_active->class();
    }

    public function getStatusAlertMessageProperty()
    {
        return 'This user is '. $this->user->is_active->label();
    }

    public function getStatusAlertMessageIconProperty()
    {
        return $this->user->is_active->icon();
    }

    public function getStatusAlertHasActionProperty()
    {
        return true;
    }

    public function getStatusAlertActionButtonClassProperty()
    {
        $isActive = !$this->user->is_active->value;
        $statusEnum = UserStatusEnum::tryFrom($isActive);

        return $statusEnum->class();
    }

    public function getStatusAlertActionButtonNameProperty()
    {
        $isActive = !$this->user->is_active->value;
        $statusEnum = UserStatusEnum::tryFrom($isActive);

        return $statusEnum->buttonName();
    }

    /**
     * Initialize form attributes
     */
    public function formInit()
    {
        if (empty($this->user)) {
            $this->user = new User();
            $this->user->name = null;
            $this->user->email = null;
            $this->user->affiliate_id = null;
        } else {
            $this->selectedRole = $this->user->roles()->first()->name;
        }

        $this->userEmail = $this->user->email;

        $this->roles = Role::basicSelect()
            ->withRoleType(auth()->user())
            ->get();
    }

    /**
     * Form submission action
     */
    public function submit()
    {
        $this->validate();

        if (! empty($this->user->id)) {
            $this->update();
        } else {
            $this->store();
        }
    }

    /**
     * Create new user
     */
    public function store()
    {
        $this->user->is_active = 1;

        if (! auth()->user()->isMasterAdmin() && app('domain')) {
            $this->user->account_id = app('domain')->getClientId();
        }

        $this->user->abbreviation = $this->user->getAbbreviation();
        $this->user->save();

        $this->user->metadata()->create([
            'invited_by' => auth()->user()->id,
        ]);

        $this->user->save();


        if(auth()->user()->can('manageRole', auth()->user()) && isset($this->selectedRole)) {
            $this->user->syncRoles([$this->selectedRole]);

            $this->user->invited_by = auth()->user()->id;


            if (auth()->user()->isMasterAdmin()) {
                $this->user->assignRole(Role::getMasterRole());
            }
        } else {
            //Default Role Assign
            $this->user->assignRole(Role::getDefaultRole());
        }

        //send notifications
        UserCreated::dispatch($this->user);

        session()->flash('success', 'User saved!');

        return redirect()->route('core.user.show', [
            'user' => $this->user->id,
        ]);
    }

    /**
     * Update existing user
     */
    public function update()
    {
        $this->user->abbreviation = $this->user->getAbbreviation();

        $this->user->email = $this->userEmail;
        $this->user->save();

        if(auth()->user()->can('manageRole', auth()->user())) {
            if ($this->user->roles->first()?->name != $this->selectedRole) {
                $this->user->roles()->detach();
                $this->user->syncRoles([$this->selectedRole]);
            }
        }

        $this->editRecord = false;
        session()->flash('success', 'User updated!');
    }

    public function closeModal()
    {
        $this->deactivate_modal = false;
    }

    /** Update User Status */
    public function updateStatus()
    {
        $this->authorize('update', $this->user);

        $isActive = !$this->user->is_active->value;
        $statusEnum = UserStatusEnum::tryFrom($isActive);

        $this->user->is_active = $statusEnum;
        $this->user->save();
    }
}
