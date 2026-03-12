<?php

namespace Multek\CustomerEngagement\Channels;

use Illuminate\Notifications\Notification as LaravelNotification;
use Multek\CustomerEngagement\DTOs\Notification;
use Multek\CustomerEngagement\EngagementManager;

class EngagementChannel
{
    public function __construct(
        protected EngagementManager $manager,
    ) {}

    public function send(object $notifiable, LaravelNotification $notification): void
    {
        $message = $notification->toEngagement($notifiable);

        if (is_string($message)) {
            $message = Notification::create($message);
        }

        $externalId = $notifiable->routeNotificationFor('engagement', $notification)
            ?? $notifiable->getKey();

        $this->manager->sendToUser($externalId, $message);
    }
}
