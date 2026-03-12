<?php

namespace Multek\CustomerEngagement\Events;

class NotificationFailed
{
    public function __construct(
        public readonly string $driver,
        public readonly string $message,
        public readonly int $statusCode,
    ) {}
}
