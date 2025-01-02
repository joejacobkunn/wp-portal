<?php
 namespace App\Http\Livewire\Scheduler\Drivers\Form;

use App\Models\Scheduler\StaffInfo;
use Illuminate\Validation\Rule;
use Livewire\Form;
use Illuminate\Support\Str;

class DriversForm extends Form
{
    public ?StaffInfo $staffInfo;

    public $description;
    public $user_id;
    public $title;
    public $user_image;

    protected $validationAttributes = [
        'description' => 'Description',
        'user_id' => 'User',
        'title' => 'TItle',
        'user_image' => 'Image'
    ];

    protected function rules()
    {    return  [

            'description' => 'nullable',
            'user_image' => 'nullable',
        ];
    }

    public function init(StaffInfo $staffInfo)
    {
        $this->staffInfo = $staffInfo;
        $this->fill($staffInfo->toArray());
    }

    public function update()
    {
        $validatedData = $this->validate();
        unset($validatedData['user_image']);
        $staffInfo = $this->staffInfo->update($validatedData);

        if ($this->user_image && !is_string($this->user_image)) {
            // Clear old media first (optional)
            $this->staffInfo->clearMediaCollection(StaffInfo::DOCUMENT_COLLECTION);

            $this->staffInfo
                ->syncFromMediaLibraryRequest($this->user_image)
                ->toMediaCollection(StaffInfo::DOCUMENT_COLLECTION);

        }


    }
}

