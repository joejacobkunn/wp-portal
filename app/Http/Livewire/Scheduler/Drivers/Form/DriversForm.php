<?php
 namespace App\Http\Livewire\Scheduler\Drivers\Form;

use App\Models\Core\User;
use Illuminate\Validation\Rule;
use Livewire\Form;

class DriversForm extends Form
{
    public ?User $user;

    public $user_image;
    public $skills;
    public $tags = [];

    protected $validationAttributes = [

        'user_image' => 'Image'
    ];

    protected function rules()
    {    return  [

            'user_image' => 'nullable',
            'tags' => 'required|array'
        ];
    }

    public function init(User $user)
    {
        $this->user = $user;
        $tags = $this->user->skills?->skills;
        if($tags) {
            $this->tags = explode(",", $tags);
        }
    }

    public function update()
    {
        $validatedData = $this->validate();
        $skills = implode(",", $this->tags);
        $this->user->skills()->updateOrCreate(
            ['user_id' => $this->user->id],
            ['skills' => $skills]
        );

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

