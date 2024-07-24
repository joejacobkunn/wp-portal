<?php

namespace App\Http\Livewire\Component\Sms;

use App\Contracts\SmsInterface;
use App\Models\SMS\KenectCache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
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
    public $messageOffset =0;
    public $messageLimit =5;
    public $loadMoreBtn =false;

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
            $this->dispatch('displayMessage', status: 404, message: 'Unable to find or create user');
            return;
        }

        $this->loadmessage();
    }

    public function loadmessage($offset=0, $limit=5)
    {
        $sms = app(SmsInterface::class);

        if (isset($this->apiUser['newUser'])) {
            return;
        }

        $data = $sms->getMessages([
            'limit'=> $limit,
            'userIds' =>$this->mock ? config('kenect.demo_user') : $this->apiUser['user_id'],
            'offset' => $offset,
            'locationIds' =>config('kenect.location')
        ]);

        if ($data['status'] != 200) {
            $this->dispatch('displayMessage', status: $data['status'], message: 'failed to fetch messages');
        }

        $this->loadMoreBtn = true;
        if ($data['body']->totalConversationsCount < $this->messageLimit) {
            $this->loadMoreBtn = false;
        }

        foreach($data['body']->conversationModels as $item) {
            foreach($item->messages as $message) {
                $this->userMessages[] = [
                    'name'=> $this->apiUser['first_name'],
                    'created_at'=> Carbon::createFromTimestampMs($item->createdDate)
                                ->format(config('app.default_datetime_format')),
                    'message'=> $message->body,
                ];
            }
        }
    }

    public function findUser()
    {
        $sms = app(SmsInterface::class);

        if ($this->phone) {
            $record = $this->searchUser($sms, $this->mock ? config('kenect.demo_phone') : $this->phone);
        }

        if (empty($record) && $this->email ) {
            $record = $this->searchUser($sms, $this->mock ? config('kenect.demo_email') : $this->email);
        }

        if (!empty($record['body'])) {
            $item = $record['body'][0];
            $data =[
                'user_id' => $item->id,
                'first_name' =>  $item->firstName,
                'last_name' =>  $item->lastName,
                'email' => $item->emailAddress,
                'location_id' => $item->locationId,
                'phone' => $this->phone,
            ];

            KenectCache::create($data);
            return $data;
        }
       return;
    }

    public function createUser()
    {
        $sms = app(SmsInterface::class);
        $record = $sms->create([
            "emailAddress"=> $this->mock ? config('kenect.demo_email') : $this->email,
            "locationId"=> config('kenect.location'),
            "phoneNumbers"=> [
                [
                "number"=> $this->mock ? config('kenect.demo_phone') : $this->phone,
                "primary"=> true,
                "status"=> "true",
                "smsCapable"=> true
                ]
            ]
        ]);

        if ($record['status'] != 200) {
           return;
        }

        $data =[
            'user_id' => $record['body']->id,
            'first_name' =>  $record['body']->firstName,
            'last_name' =>  $record['body']->lastName,
            'email' => $record['body']->emailAddress,
            'location_id' => $record['body']->locationId,
            'phone' => $record['body']->phoneNumbers[0]->number,
        ];

        KenectCache::create($data);
        $data['newUser'] =true;
        return $data;
    }

    public function sendMessage()
    {
        $sms = app(SmsInterface::class);
        $this->validate();
        $response = $sms->send([
            'phone' => $this->mock ? config('kenect.demo_phone') : $this->phone,
            'message' => $this->newMessage,
            'locationId' =>config('kenect.location'),
        ]);
        if ($response['status']!=201) {
            $this->addError('newMessage', 'sending failed');
            return;
        }

        $this->userMessages[] = [
            'name'=> $this->apiUser['first_name'],
            'created_at'=> now(),
            'message'=> $this->newMessage,
        ];
        $this->reset(['newMessage']);
    }

    public function searchUser($sms, $key)
    {
        $record = $sms->getUser([
            'searchString' =>$key,
            'limit'=> 1,
            'locationIds' =>config('kenect.location')
        ]);
        return $record;
    }

    public function render()
    {
        return view('livewire.component.sms.messages');
    }
}
