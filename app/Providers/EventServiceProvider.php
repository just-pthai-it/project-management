<?php

namespace App\Providers;

use App\Events\TaskStatusUpdatedToReviewOrCompleteEvent;
use App\Listeners\SystemActivityEventSubscriber;
use App\Listeners\TaskStatusUpdatedToReviewOrCompleteListener;
use App\Listeners\UserImpactedSubscriber;
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
        Registered::class                               => [
            SendEmailVerificationNotification::class,
        ],
        TaskStatusUpdatedToReviewOrCompleteEvent::class => [
            TaskStatusUpdatedToReviewOrCompleteListener::class,
        ],
    ];

    protected $subscribe = [
        SystemActivityEventSubscriber::class,
        UserImpactedSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot ()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents ()
    {
        return false;
    }
}
