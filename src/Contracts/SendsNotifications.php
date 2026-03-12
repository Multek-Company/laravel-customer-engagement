<?php

namespace Multek\CustomerEngagement\Contracts;

use Multek\CustomerEngagement\DTOs\Notification;

interface SendsNotifications
{
    public function sendToUser(string $externalId, Notification $notification): array;

    public function sendToUsers(array $externalIds, Notification $notification): array;

    public function sendToSegment(string $segment, Notification $notification): array;
}
