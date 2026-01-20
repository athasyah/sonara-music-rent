<?php

namespace App\Listeners;

use App\Enums\StatusEnum;
use App\Events\InstrumentConditionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateInstrumentStatusFromCondition
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
    public function handle(InstrumentConditionCreated $event): void
    {
        $condition = $event->condition;

        $status = match ($condition->condition) {
            'good'          => StatusEnum::AVAILABLE->value,
            'minor_damage'  => StatusEnum::MAINTENANCE->value,
            'major_damage'  => StatusEnum::MAINTENANCE->value,
            default         => StatusEnum::AVAILABLE->value,
        };

        $condition->instrument()->update([
            'status' => $status
        ]);
    }
}
