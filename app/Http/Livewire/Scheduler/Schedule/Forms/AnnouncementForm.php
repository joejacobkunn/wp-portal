<?php
 namespace App\Http\Livewire\Scheduler\Schedule\Forms;

use App\Models\Scheduler\Announcement;
use App\Models\Scheduler\TruckSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Form;

 class AnnouncementForm extends Form
 {
    public ?Announcement $announcement;

    public $message;

    protected $validationAttributes = [
        'message' => 'Message',
    ];

    protected $rules = [
        'message' =>'required|string:max:250'
    ];

    public function init(Announcement $announcement)
    {
        $this->announcement = $announcement;
        $this->fill($announcement->toArray());
    }

    public function store($whseShort)
    {
        $validatedData = $this->validate();
        Announcement::create(
            [
                'whse' => $whseShort,
                'message' => $this->message,
                'created_by' => Auth::User()->id,
            ]
        );
    }

    public function delete(Announcement $announcement)
    {
        return $announcement?->delete();
    }
 }
