<?php

namespace App\Providers;

use App\Events\InstrumentConditionCreated;
use App\Events\RentalStatusUpdated;
use App\Listeners\UpdateInstrumentStatus;
use App\Listeners\UpdateInstrumentStatusFromCondition;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        InstrumentConditionCreated::class => [UpdateInstrumentStatusFromCondition::class,],
        RentalStatusUpdated::class => [UpdateInstrumentStatus::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
