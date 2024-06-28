<?php

namespace App\Http\Livewire\Component\Sms;

use App\Contracts\SmsInterface;
use Livewire\Component;

class Messages extends Component
{
    public $phone;
    public $email;
    public $userId;
    public $messages;
    public function mount()
    {
        $this->findUser();
        $this->loadmessage();
    }
    public function loadmessage()
    {
            $sms = app(SmsInterface::class);
            $result = $sms->getMessages([
                'userIds' =>$this->userId,
                'limit'=> 10,
                'locationIds' =>config('kenect.KENECT_LOCATION')
            ]);
            $data = json_decode($result, true);

        $this->messages=[
            0=>[
                'name'=> 'arun',
                'created_at'=> '34-34-3',
                'message'=> 'lorrem ipsum msg dont try too hard',
            ],
            1=>[
                'name'=> 'arun',
                'created_at'=> '34-34-3',
                'message'=> 'lorrem ipsum msg dont try too hard',
            ]
            ];

    }
    public function findUser()
    {
        $sms = app(SmsInterface::class);

        if ($this->phone) {
            $result = $sms->getUser([
                'searchString' =>$this->phone,
                'limit'=> 1,
                'locationIds' =>config('kenect.KENECT_LOCATION')
            ]);
            $data = json_decode($result, true);
            $this->userId = $data[0]['id'];
        }

        if ($this->email && !$this->userId ) {
            $result = $sms->getUser([
                'searchString' =>$this->email,
                'limit'=> 1,
                'locationIds' =>config('kenect.KENECT_LOCATION')
            ]);
            $data = json_decode($result, true);
            $this->userId = $data[0]['id'];
        }
        if ($this->userId) {
            $this->loadmessage();
        } else {
            $this->createUser();
        }


    }
    public function render()
    {
        return view('livewire.component.sms.messages');
    }
}
