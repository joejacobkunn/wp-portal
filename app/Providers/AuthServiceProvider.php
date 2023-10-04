<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
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
            \App\Models\Report::class => \App\Policies\ReportingPolicy::class,
        ];
    }

}
