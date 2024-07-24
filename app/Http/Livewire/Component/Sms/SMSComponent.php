<?php
namespace App\Http\Livewire\Component\Sms;

use App\Models\SMS\KenectCache;
use App\Services\KenectSms;
use Livewire\Component;

class SMSComponent extends Component
{
    public $phone;
    public $email;
    public $apiUser;
    public $alert=[
        'status' =>false,
        'code' =>null,
        'message' =>null,
    ];

    //listeners
    protected $listeners = [
        'displayMessage' => 'displayMessage'
    ];

    public function render()
    {
         return view('livewire.component.sms.sms-component');
    }
    public function mount()
    {
        if (!$this->phone ) {
            $this->dispatch('displayMessage', status: 404, message: 'Please provide a phone number!');
            return;
        }
        $this->checkUserIncache();
    }

    public function checkUserIncache()
    {
        $user = KenectCache::where('phone', $this->phone)
            ->orWhere('email', $this->email)->first();

        if ($user) {
            $this->apiUser = [
                'id' => $user->id,
                'user_id' =>$user->user_id,
                'first_name' => $user->first_name,
                'last name' => $user->lat_name,
                'email' => $user->email,
                'locationId' => $user->location_id,
                'phone' => $user->phone,
            ];
        }
    }

    public function displayMessage($status, $message) {
        $this->alert['status'] = true;
        $this->alert['code'] = $status;
        $this->alert['message'] = $message;
    }
}

