<?php

namespace Multek\CustomerEngagement\Contracts;

use Multek\CustomerEngagement\DTOs\CustomerEvent;

interface TracksEvents
{
    public function trackEvent(CustomerEvent $event): void;

    public function trackEvents(array $events): void;
}
