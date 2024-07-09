<?php

namespace App\Http\Livewire\Component\Sms;

use App\Contracts\SmsInterface;
use App\Models\SMS\KenectCache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Messages extends Component
{
    use LivewireAlert
    ;
    public $phone;
    public $email;
    public $userId;
    public $apiUser;
    public $sms;
    public $userMessages=[];
    public function mount()
    {
        $this->findUser();
    }
    public function loadmessage()
    {
            // $data = $this->sms->getMessages([
            //     'userIds' =>$this->userId,
            //     'limit'=> 10,
            //     'locationIds' =>config('kenect.KENECT_LOCATION')
            // ]);
        $this->userMessages=[
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
        if (!$this->apiUser ) {
            $this->userId = $this->apiUser->account_id;
            $this->loadmessage();
            return;
        }

        if ($this->phone) {
            // $data = $this->sms->getUser([
            //     'searchString' =>$this->phone,
            //     'limit'=> 1,
            //     'locationIds' =>config('kenect.KENECT_LOCATION')
            // ]);
            $data=1;
            if (!empty($data)) {
                //$this->userId = $data[0]['id'];
                $this->userId = 1;
            }
        }

        if ($this->email && !$this->userId ) {
            // $data = $this->sms->getUser([
            //     'searchString' =>$this->email,
            //     'limit'=> 1,
            //     'locationIds' =>config('kenect.KENECT_LOCATION')
            // ]);

            if (!empty($data)) {
                //$this->userId = $data[0]['id'];
            }
        }

        if ($this->userId) {
            $this->loadmessage();
            return;
        }
        $this->createUser();
    }

    public function createUser()
    {
        // $data = $this->sms->create([
        //     "emailAddress"=> $this->email,
        //     "locationId"=> config('kenect.KENECT_LOCATION'),

        //     "phoneNumbers"=> [
        //       [
        //         "number"=> $this->phone,
        //         "primary"=> true,
        //         "status"=> "true",
        //         "smsCapable"=> true
        //       ]
        //     ]
        // ]);
        if(empty($data)){
            $this->alert('error','failed to create new user!');
            return false;
        }

        $this->userId = 12;
        KenectCache::create([
            'phone'=>$this->phone,
            'user_id'=>$this->userId,
            'email'=>$this->email
        ]);

    }

    public function sendMessage()
    {
        sleep(5);
    }

    public function render()
    {
        return view('livewire.component.sms.messages');
    }
}
