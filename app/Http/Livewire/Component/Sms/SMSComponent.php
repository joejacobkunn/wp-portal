<?php
namespace App\Http\Livewire\Component\Sms;

use App\Models\SMS\KenectCache;
use App\Services\KenectSms;
use Livewire\Component;

class SMSComponent extends Component
{
    public $phone;
    public $email;
    public $isLoading=true;
    public $apiUser;
    public $alert=false;

    public function render()
    {
         return view('livewire.component.sms.sms-component');
    }
    public function mount()
    {
        if (!$this->phone || !$this->email) {
            $this->alert=true;
            $this->isLoading=false;
            return;
        }

        $this->checkUserIncache();
    }

    public function checkUserIncache()
    {
        $user = KenectCache::where('phone', $this->phone)
            ->orWhere('email', $this->email)->first();

        if ($user) {
            $this->apiUser = $data = [
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
}
