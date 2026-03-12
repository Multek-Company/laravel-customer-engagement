<?php

namespace Multek\CustomerEngagement\Events;

class NotificationSent
{
    public function __construct(
        public readonly string $driver,
        public readonly ?string $notificationId,
        public readonly array $response,
    ) {}
}
