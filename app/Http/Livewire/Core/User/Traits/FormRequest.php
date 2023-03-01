<?php

namespace App\Http\Livewire\Core\User\Traits;

use App\Models\Core\Affiliate;
use App\Models\Core\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            'user.email' => 'required|email|unique:users,email' . ($this->user ? ',' .$this->user->id : ''),
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

        if (!empty($this->user->id)) {
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
        $this->user->invited_by = auth()->user()->id;
        $this->user->password = "password";
        $this->user->save();

        session()->flash('success', 'User saved!');
        return redirect()->route('core.user.show', [
            'user' => $this->user->id
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
}