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

    protected function rules()
    {
        return [
            'user.name' => 'required',
            'user.email' => 'required|email|unique:users,email'.($this->user->email ? ','.$this->user->id : ''),
            'selectedRole' => 'required',
        ];
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
        }

        $this->roles = Role::ofAccount(auth()->user()->account_id)
            ->whereNot('name', 'super-admin-account-'.auth()->user()->account_id)
            ->basicSelect()
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
        $this->user->password = 'password';

        if (! auth()->user()->isMasterAdmin() && app('domain')) {
            $this->user->account_id = app('domain')->getClientId();
        }

        $this->user->abbreviation = $this->user->getAbbreviation();
        $this->user->save();

        $this->user->metadata()->create([
            'invited_by' => auth()->user()->id,
        ]);

        $this->user->save();

        if($this->selectedRole != 'super-admin-account-'.$this->user->account_id) {
            $this->user->assignRole($this->selectedRole);
        }

        $this->user->invited_by = auth()->user()->id;

        if (auth()->user()->isMasterAdmin()) {
            $this->user->assignRole(Role::getMasterRole());
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
    // @TODO Remove after confirmation on WP-8 Remove User info Edit
    // public function update()
    // {
    //     $this->user->abbreviation = $this->user->getAbbreviation();

    //     $this->user->save();

    //     if ($this->user->roles->first()?->name != $this->selectedRole) {
    //         $this->user->roles()->detach();
    //         $this->user->assignRole($this->selectedRole);
    //     }

    //     $this->editRecord = false;
    //     session()->flash('success', 'User updated!');
    // }

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
