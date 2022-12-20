<?php

namespace App\Providers;

// use Illuminate\Auth\Events\Registered;
// use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Events\AccessTokenCreated;
// use App\Events\NewAnnouncementWasCreatedEvent;
// use App\Listeners\SendNotificationsToSubscribedUsersListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\NewAnnouncementWasCreatedEvent::class => [
            \App\Listeners\V1\SendNotificationsToSubscribedUsersListener::class,
        ],
        \App\Events\V2\NewAnnouncementWasCreatedEvent::class => [
            \App\Listeners\V2\SendNotificationsToSubscribedUsersListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
