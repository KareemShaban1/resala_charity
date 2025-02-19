<?php

namespace App\Providers;

use App\Models\Donation;
use App\Models\MonthlyForm;
use App\Models\MonthlyFormCancellation;
use App\Observers\DonationObserver;
use App\Observers\EventObserver;
use App\Observers\MonthlyFormCancellationObserver;
use App\Observers\MonthlyFormObserver;
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
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
        MonthlyForm::observe(MonthlyFormObserver::class);
        Donation::observe(DonationObserver::class);
        Event::observe(EventObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
