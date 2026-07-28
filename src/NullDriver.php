<?php

namespace Multek\CustomerEngagement;

use Multek\CustomerEngagement\Contracts\EngagementDriver;
use Multek\CustomerEngagement\Contracts\SendsNotifications;
use Multek\CustomerEngagement\Contracts\SyncsUsers;
use Multek\CustomerEngagement\Contracts\TracksEvents;
use Multek\CustomerEngagement\DTOs\Customer;
use Multek\CustomerEngagement\DTOs\CustomerEvent;
use Multek\CustomerEngagement\DTOs\Notification;

class NullDriver implements EngagementDriver, SendsNotifications, SyncsUsers, TracksEvents
{
    public function getName(): string
    {
        return 'null';
    }

    public function getUser(string $externalId): array
    {
        return [];
    }

    public function createUser(Customer $customer): array
    {
        return [];
    }

    public function updateUser(Customer $customer): array
    {
        return [];
    }

    public function deleteUser(string $externalId): void {}

    public function sendToUser(string $externalId, Notification $notification): array
    {
        return [];
    }

    public function sendToUsers(array $externalIds, Notification $notification): array
    {
        return [];
    }

    public function sendToSegment(string $segment, Notification $notification): array
    {
        return [];
    }

    public function trackEvent(CustomerEvent $event): void {}

    public function trackEvents(array $events): void {}
}
