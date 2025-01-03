<?php
 namespace App\Http\Livewire\Scheduler\Drivers\Form;

use App\Models\Core\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

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
            $this->user->clearMediaCollection(User::DOCUMENT_COLLECTION);

            $this->user
                ->syncFromMediaLibraryRequest($this->user_image)
                ->toMediaCollection(User::DOCUMENT_COLLECTION);

        }


    }
}

