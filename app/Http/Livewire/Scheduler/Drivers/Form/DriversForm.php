<?php
 namespace App\Http\Livewire\Scheduler\Drivers\Form;

use App\Models\Core\User;
use App\Models\Scheduler\StaffInfo;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Illuminate\Support\Str;

class DriversForm extends Form
{
    public ?User $user;

    public $user_image;

    protected $validationAttributes = [

        'user_image' => 'Image'
    ];

    protected function rules()
    {    return  [

            'user_image' => 'nullable',
        ];
    }

    public function init(User $user)
    {
        $this->user = $user;
    }

    public function update()
    {
        $validatedData = $this->validate();
        unset($validatedData['user_image']);
        if ($this->user_image && !is_string($this->user_image)) {
            // Clear old media first (optional)
            $this->user->clearMediaCollection(StaffInfo::DOCUMENT_COLLECTION);

            $this->user
                ->syncFromMediaLibraryRequest($this->user_image)
                ->toMediaCollection(StaffInfo::DOCUMENT_COLLECTION);

        }


    }
}

