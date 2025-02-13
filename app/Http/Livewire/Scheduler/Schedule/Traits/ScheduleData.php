<?php

namespace App\Http\Livewire\Scheduler\Schedule\Traits;

use Carbon\Carbon;
use App\Models\Scheduler\Schedule;
use Illuminate\Database\Eloquent\Builder;

trait ScheduleData
{
    /**
     * HRAF Get All HRAF Data Builder
     */
    public function scheduleBaseQuery(): Builder
    {
        return Schedule::leftJoin('truck_schedules', 'truck_schedules.id', '=', 'schedules.truck_schedule_id')
                ->whereNull('truck_schedules.deleted_at')
                ->leftJoin('orders', 'orders.order_number', '=', 'schedules.sx_ordernumber')
                ->whereNull('orders.deleted_at')
                ->leftJoin('customers', 'orders.sx_customer_number', '=', 'customers.sx_customer_number')
                ->whereNull('customers.deleted_at')
                ->leftJoin('users', 'schedules.created_by', '=', 'users.id')
                ->whereNull('users.deleted_at');
    }

    /**
     * HRAF Get All HRAF Data Builder with Pending Approval Status
     */
    public function queryByDate($date): Builder
    {
        return $this->scheduleBaseQuery()->whereDate('schedules.schedule_date', $date);
    }

    /**
     * HRAF Get All HRAF Data Builder with Pending Approval Status
     */
    public function queryByStatus($status): Builder
    {
        $scheduleQuery = $this->scheduleBaseQuery();

        if ($status == 'unconfirmed') {
            $scheduleQuery->where('schedules.status', 'scheduled');
            $scheduleQuery->where('schedules.schedule_date', '>=', Carbon::now()->toDateString());
        }

        return $scheduleQuery;
    }
}
