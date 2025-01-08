<?php
 namespace App\Http\Livewire\Scheduler\NotificationTemplate\Form;

use App\Models\Scheduler\NotificationTemplate;
use Illuminate\Validation\Rule;
use Livewire\Form;

class NotificationForm extends Form
{
    public NotificationTemplate $template;
    public $name;
    public $emailContent;
    public $emailSubject;
    public $smsContent;
    public $is_active = true;


    protected $validationAttributes = [
        'name' => 'Name',
        'emailContent' => 'Email Content',
        'smsContent' => 'SMS Content',
    ];

    protected function rules()
    {
        return [
            'emailSubject' => 'required',
            'emailContent' => 'required',
            'smsContent' => 'nullable',
        ];
    }

    public function init(NotificationTemplate $template)
    {
        $this->template = $template;
        $this->fill($template->toArray());
        $this->emailContent = $this->template->email_content;
        $this->emailSubject = $this->template->email_subject;
        $this->smsContent = $this->template->sms_content;
    }

    public function update()
    {
        $validatedData = $this->validate();

        $this->template->fill($validatedData);
        $this->template->email_content = $this->emailContent;
        $this->template->email_subject =  $this->emailSubject ;
        $this->template->sms_content = $this->smsContent;
        $this->template->save();
    }
}
