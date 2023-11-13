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
            'location.location' => 'required',
            'location.is_active' => 'required|boolean',
            'location.fortis_location_id' => 'nullable'
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
        $this->location->location = null;
        $this->location->fortis_location_id = null;
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

        $this->editRecord = false;
        session()->flash('success', 'Account updated!');
    }


}
