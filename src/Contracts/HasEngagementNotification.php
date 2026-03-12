<?php

namespace Multek\CustomerEngagement\Contracts;

use Multek\CustomerEngagement\DTOs\Notification;

interface HasEngagementNotification
{
    public function toEngagement(object $notifiable): Notification|string;
}
