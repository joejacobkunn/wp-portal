<?php

namespace App\Providers;

use App\Events\User\UserCreated;
use App\Listeners\User\UserCreatedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserCreated::class => [
            UserCreatedNotification::class,
        ],

        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Azure\AzureExtendSocialite::class.'@handle',
        ],

        \App\Events\Orders\OrderCancelled::class => [
            \App\Listeners\Orders\OrderCancelledListener::class,
        ],

        \App\Events\Orders\OrderFollowUp::class => [
            \App\Listeners\Orders\OrderFollowUpListener::class,
        ],


        \App\Events\Orders\OrderBreakTie::class => [
            \App\Listeners\Orders\OrderBreakTieListener::class,
        ],

        \App\Events\Floormodel\InventoryAdded::class => [
            \App\Listeners\Floormodel\InventoryAddedListener::class
        ],

        \App\Events\Floormodel\InventoryUpdated::class => [
            \App\Listeners\Floormodel\InventoryUpdatedListener::class
        ],

        \App\Events\Floormodel\InventoryDeleted::class => [
            \App\Listeners\Floormodel\InventoryDeletedListener::class
        ],





        
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
