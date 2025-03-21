<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Equipment\Warranty\BrandConfigurator\BrandWarranty;
use App\Models\SalesRepOverride\SalesRepOverride;
use App\Models\Scheduler\NotificationTemplate;
use App\Models\Scheduler\Schedule;
use App\Models\Scheduler\Shifts;
use App\Models\Scheduler\Zipcode;
use App\Models\Scheduler\Zones;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->policies = $this->getAllPolicies();
        $this->registerPolicies();
    }

    /**
     * Get all policies
     */
    public function getAllPolicies()
    {
        $policies = [];
        $policies += $this->getCorePolicies();
        $policies += $this->getVehiclePolicies();
        $policies += $this->getReportingPolicies();
        $policies += $this->getOrderPolicies();
        $policies += $this->getWarrantyPolicies();
        $policies += $this->getFloorModelPolicies();
        $policies += $this->getSalesRepOverride();
        $policies += $this->getServiceAreaPolicies();
        $policies += $this->getSchedulesPolicies();
        return $policies;
    }

    /**
     * Get core policies
     */
    private function getCorePolicies()
    {
        return [
            \App\Models\Core\User::class => \App\Policies\Core\UserPolicy::class,
            \App\Models\Core\Role::class => \App\Policies\Core\RolePolicy::class,
            \App\Models\Core\Account::class => \App\Policies\Core\AccountPolicy::class,
            \App\Models\Core\Location::class => \App\Policies\Core\LocationPolicy::class,
            \App\Models\Core\Module::class => \App\Policies\Core\ModulePolicy::class,
            \App\Models\Core\Customer::class => \App\Policies\Core\CustomerPolicy::class
        ];
    }

    private function getVehiclePolicies()
    {
        return [
            \App\Models\Vehicle\Vehicle::class => \App\Policies\Vehicle\VehiclePolicy::class,
        ];
    }

    private function getReportingPolicies()
    {
        return [
            \App\Models\Report\Report::class => \App\Policies\ReportingPolicy::class,
            \App\Models\Report\Dashboard::class => \App\Policies\ReportDashboardPolicy::class
        ];
    }

    private function getOrderPolicies()
    {
        return [
            \App\Models\Order\Order::class => \App\Policies\Order\OrderPolicy::class,
        ];
    }
    private function getWarrantyPolicies()
    {
        return [
            BrandWarranty::class => \App\Policies\Equipment\Warranty\BrandConfigurator\BrandWarrantyPolicy::class
        ];
    }

    private function getFloorModelPolicies()
    {
        return [
            \App\Models\Equipment\FloorModelInventory\FloorModelInventory::class => \App\Policies\Equipment\FloorModel\FloorModelInventoryPolicy::class,
            \App\Models\Equipment\FloorModelInventory\FloorModelInventoryNote::class => \App\Policies\Equipment\FloorModel\FloorModelInventoryNotePolicy::class
        ];
    }
    private function getSalesRepOverride()
    {
        return [
            SalesRepOverride::class => \App\Policies\SalesRepOverride\SalesRepOverridePolicy::class
        ];
    }


    private function getServiceAreaPolicies()
    {
        return [
            Zones::class => \App\Policies\Scheduler\ServiceArea\ZonesPolicy::class,
            Zipcode::class => \App\Policies\Scheduler\ServiceArea\ZipcodePolicy::class
        ];
    }

    public function getSchedulesPolicies()
    {
        return [
            Schedule::class => \App\Policies\Scheduler\Schedule\SchedulesPolicy::class,
            Shifts::class => \App\Policies\Scheduler\ShiftPolicy::class,
            NotificationTemplate::class => \App\Policies\Scheduler\TemplatePolicy::class,
            \App\Models\Scheduler\Truck::class => \App\Policies\Scheduler\TruckPolicy::class,
            \App\Models\Scheduler\CargoConfigurator::class => \App\Policies\Scheduler\CargoConfiguratorPolicy::class,
            \App\Models\Scheduler\Announcement::class => \App\Policies\Scheduler\Schedule\AnnouncementPolicy::class,
        ];
    }
}
