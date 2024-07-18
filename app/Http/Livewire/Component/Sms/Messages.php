<?php

namespace App\Http\Livewire\Component\Sms;

use App\Contracts\SmsInterface;
use App\Models\SMS\KenectCache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Messages extends Component
{
    use LivewireAlert, WithFileUploads;

    public $phone;
    public $email;
    public $apiUser;
    public $sms;
    public $newMessage;
    public $mock;
    public $userMessages=[];
    public $messageOffset =1;
    protected  $rules =[
        'newMessage' => 'required',
    ];

    public function validationAttributes()
    {
        return [
            'newMessage' => 'Message',
        ];
    }
    public function mount()
    {
        $this->mock = config('kenect.mock');

        if (!$this->apiUser) {
            $this->apiUser = $this->findUser() ?? $this->createUser();
        }

        if (!$this->apiUser) {
            $this->alert('error','Unable to find or create user');
            return;
        }

        $this->loadmessage();
    }

    public function loadmessage($offset=null, $limit=2)
    {
        if (isset($this->apiUser['newUser'])) {
            return;
        }
        if ($this->mock) {
            $userMessages=[
                0=>[
                    'name'=> $this->apiUser['name'],
                    'created_at'=> fake()->dateTimeThisYear->format(config('app.default_datetime_format')),
                    'message'=> fake()->sentence(),
                ],
                1=>[
                    'name'=> $this->apiUser['name'],
                    'created_at'=> fake()->dateTimeThisYear->format(config('app.default_datetime_format')),
                    'message'=> fake()->sentence(),
                ]
            ];

            $this->userMessages = (rand(0, 1) === 0) ? $userMessages : [];
        } else {
            //check : new user if true return [];
            // fetch messages using api
        }

    }

    public function findUser()
    {
        if ($this->mock) {
            $data =[
                'user_id' => rand(100, 999),
                'name' =>  fake()->name(),
                'last name' =>  fake()->name(),
                'email' => $this->email,
                'locationId' => rand(1000, 9999),
                'phone' => $this->phone,
            ];
          $data =  (rand(0, 1) === 0) ? $data : [];
        } else {
            //api call getuser
        }

       if (!empty($data)) {
           KenectCache::create($data);
           return $data;
       }
       return;
    }

    public function createUser()
    {
        if ($this->mock) {
            $data = [
                'user_id' => rand(100, 999),
                'name' =>  null,
                'last name' =>  null,
                'email' => $this->email,
                'locationId' => rand(1000, 9999),
                'phone' => rand(1000000000, 9999999999),
            ];
        } else {
            // populate $data with values from api
        }
        $user = KenectCache::create($data);
        $data['newUser'] =true;
        return $data;

    }

    public function sendMessage()
    {
        $this->validate();
        if ($this->mock) {
            sleep(3);
            $this->userMessages[] = [
                'name'=> $this->apiUser['name'],
                'created_at'=> now(),
                'message'=> $this->newMessage,
            ];
            $this->reset(['newMessage']);
        }
    }

    public function loadMoreMessages($offset=null,$limit=5)
    {
        if ($this->mock) {
            $userMessages=[
                0=>[
                    'name'=> $this->apiUser['name'],
                    'created_at'=> fake()->dateTimeThisYear->format(config('app.default_datetime_format')),
                    'message'=> fake()->sentence(),
                ],
                1=>[
                    'name'=> $this->apiUser['name'],
                    'created_at'=> fake()->dateTimeThisYear->format(config('app.default_datetime_format')),
                    'message'=> fake()->sentence(),
                ]
            ];
            foreach($userMessages as $item) {
                $this->userMessages[] = $item;
            }
        }
    }

    public function render()
    {
        return view('livewire.component.sms.messages');
    }
}
