<?php

namespace Multek\CustomerEngagement\Facades;

use Illuminate\Support\Facades\Facade;
use Multek\CustomerEngagement\Contracts\EngagementDriver;
use Multek\CustomerEngagement\DTOs\Customer;
use Multek\CustomerEngagement\DTOs\CustomerEvent;
use Multek\CustomerEngagement\DTOs\Notification;
use Multek\CustomerEngagement\EngagementManager;

/**
 * @method static EngagementDriver driver(?string $driver = null)
 * @method static string getDefaultDriver()
 * @method static bool syncsUsers(?string $driver = null)
 * @method static bool sendsNotifications(?string $driver = null)
 * @method static bool tracksEvents(?string $driver = null)
 * @method static array getUser(string $externalId, ?string $driver = null)
 * @method static array createUser(Customer $customer, ?string $driver = null)
 * @method static array updateUser(Customer $customer, ?string $driver = null)
 * @method static void deleteUser(string $externalId, ?string $driver = null)
 * @method static array sendToUser(string $externalId, Notification $notification, ?string $driver = null)
 * @method static array sendToUsers(array $externalIds, Notification $notification, ?string $driver = null)
 * @method static array sendToSegment(string $segment, Notification $notification, ?string $driver = null)
 * @method static void trackEvent(CustomerEvent $event, ?string $driver = null)
 * @method static void trackEvents(array $events, ?string $driver = null)
 * @method static EngagementManager extend(string $driver, \Closure $callback)
 *
 * @see EngagementManager
 */
class Engagement extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EngagementManager::class;
    }
}
