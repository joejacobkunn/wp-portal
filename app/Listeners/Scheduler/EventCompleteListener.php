<?php

namespace App\Listeners\Scheduler;

use App\Events\Scheduler\EventComplete;
use App\Events\Scheduler\EventScheduled;
use App\Models\Scheduler\NotificationTemplate;
use App\Models\Scheduler\Schedule;
use App\Notifications\Scheduler\EmailNotification;
use App\Services\Kenect;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\App;
use App\Classes\SX;


class EventCompleteListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EventComplete $event): void
    {
        if(App::environment() == 'production')
        {
            $notification = $this->populateTemplate('ahm-complete',$event->schedule);

            if($event->schedule->user->phone)
            {
                $kenect = new Kenect();
                $kenect->send($event->schedule->user->phone, $notification['sms'], '18771');
            }
    
            if($event->schedule->user->email)
            {
                Notification::route('mail', $event->schedule->user->email)
                ->notify(new EmailNotification($notification['email_subject'], $notification['email_body']));
    
            }

            $sx_client = new SX();
            $sx_response = $sx_client->create_order_note('AHM #'.$event->schedule->scheduleId().' has been completed by '.$event->schedule->completedUser->name.' on '.$event->schedule->completed_at->toFormattedDayDateString(), $event->schedule->sx_ordernumber);

    
        }
    }

    public function populateTemplate($slug ,Schedule $schedule)
    {
        $template = NotificationTemplate::where('slug', $slug)->first();
        $email_subject = $this->fillTemplateVariables($template->email_subject, $schedule);
        $email_body = $this->fillTemplateVariables($template->email_content, $schedule);
        $sms = $this->fillTemplateVariables($template->sms_content, $schedule);

        return ['email_subject' => $email_subject, 'email_body' => $email_body, 'sms' => $sms];
    }

    private function fillTemplateVariables($template, $schedule)
    {
        if(Str::contains($template,'[ScheduleID]')) $template = Str::replace('[ScheduleID]', $schedule->scheduleId(), $template);
        if(Str::contains($template,'[CustomerName]')) $template = Str::replace('[CustomerName]', $schedule->order->customer->name, $template);
        if(Str::contains($template,'[TimeSlot]')) $template = Str::replace('[TimeSlot]', $schedule->truckSchedule->start_time.' - '.$schedule->truckSchedule->end_time, $template);
        if(Str::contains($template,'[ScheduledDate]')) $template = Str::replace('[ScheduledDate]', Carbon::parse($schedule->schedule_date)->toFormattedDateString(), $template);
        if(Str::contains($template,'[ServiceAddress]')) $template = Str::replace('[ServiceAddress]', str_replace(', USA','',$schedule->service_address), $template);
        if(Str::contains($template,'[Warehouse]')) $template = Str::replace('[Warehouse]', $schedule->order->warehouse->title, $template);
        if(Str::contains($template,'[WarehouseNumber]')) $template = Str::replace('[WarehouseNumber]', format_phone($schedule->order->warehouse->phone), $template);
        if(Str::contains($template,'[WarehouseAddress]')) $template = Str::replace('[WarehouseAddress]', format_phone($schedule->order->warehouse->address), $template);
        if(Str::contains($template,'[OrderNumber]')) $template = Str::replace('[OrderNumber]', $schedule->sx_ordernumber, $template);
        if($schedule->not_purchased_via_weingartz)
        {
            if(Str::contains($template,'[ServiceEquipment]')) $template = Str::replace('[ServiceEquipment]', 'equipment', $template);
        }else{
            if(Str::contains($template,'[ServiceEquipment]')) $template = Str::replace('[ServiceEquipment]', array_keys($schedule->line_item)[0], $template);
        }
        if(Str::contains($template,'[DriverName]')) $template = Str::replace('[DriverName]', $schedule->truckSchedule->driver?->name, $template);
        return $template;
    }

}
