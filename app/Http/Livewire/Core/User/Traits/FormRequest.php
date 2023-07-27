<?php

namespace App\Http\Livewire\Core\User\Traits;

use App\Events\User\UserCreated;
use App\Models\Core\User;

trait FormRequest
{
    protected $validationAttributes = [
        'user.name' => 'Name',
        'user.email' => 'Email',
    ];

    protected function rules()
    {
        return [
            'user.name' => 'required',
            'user.email' => 'required|email|unique:users,email'.($this->user ? ','.$this->user->id : ''),
        ];
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

        if (app('domain')) {
            $this->user->account_id = app('domain')->getClientId();
        }

        $this->user->save();

        $this->user->metadata()->create([
            'invited_by' => auth()->user()->id,
        ]);

        $this->user->save();

        $this->user->invited_by = auth()->user()->id;

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
        $this->user->save();

        $this->editRecord = false;
        session()->flash('success', 'User updated!');
    }

    /**
     * Delete existing user
     */
    public function delete()
    {
        $this->user->delete();
        session()->flash('success', 'User deleted !');

        return redirect()->route('core.user.index');
    }
}
