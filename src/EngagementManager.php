<?php

namespace Multek\CustomerEngagement;

use BadMethodCallException;
use Illuminate\Support\Manager;
use Multek\CustomerEngagement\Contracts\EngagementDriver;
use Multek\CustomerEngagement\Contracts\SendsNotifications;
use Multek\CustomerEngagement\Contracts\SyncsUsers;
use Multek\CustomerEngagement\Contracts\TracksEvents;
use Multek\CustomerEngagement\DTOs\Customer;
use Multek\CustomerEngagement\DTOs\CustomerEvent;
use Multek\CustomerEngagement\DTOs\Notification;

class EngagementManager extends Manager
{
    protected const CAPABILITIES = [
        SyncsUsers::class => 'users',
        SendsNotifications::class => 'notifications',
        TracksEvents::class => 'events',
    ];

    protected ?NullDriver $noopDriver = null;

    public function getDefaultDriver(): string
    {
        return $this->config->get('customer-engagement.default', 'null');
    }

    public function createNullDriver(): NullDriver
    {
        return new NullDriver;
    }

    // ── Capability Checks ──────────────────────────────────────────────

    public function syncsUsers(?string $driver = null): bool
    {
        return $this->capabilityEnabled('users', $driver)
            && $this->driver($driver) instanceof SyncsUsers;
    }

    public function sendsNotifications(?string $driver = null): bool
    {
        return $this->capabilityEnabled('notifications', $driver)
            && $this->driver($driver) instanceof SendsNotifications;
    }

    public function tracksEvents(?string $driver = null): bool
    {
        return $this->capabilityEnabled('events', $driver)
            && $this->driver($driver) instanceof TracksEvents;
    }

    // ── SyncsUsers Pass-throughs ───────────────────────────────────────

    public function getUser(string $externalId, ?string $driver = null): array
    {
        return $this->resolveCapability(SyncsUsers::class, $driver)->getUser($externalId);
    }

    public function createUser(Customer $customer, ?string $driver = null): array
    {
        return $this->resolveCapability(SyncsUsers::class, $driver)->createUser($customer);
    }

    public function updateUser(Customer $customer, ?string $driver = null): array
    {
        return $this->resolveCapability(SyncsUsers::class, $driver)->updateUser($customer);
    }

    public function deleteUser(string $externalId, ?string $driver = null): void
    {
        $this->resolveCapability(SyncsUsers::class, $driver)->deleteUser($externalId);
    }

    // ── SendsNotifications Pass-throughs ───────────────────────────────

    public function sendToUser(string $externalId, Notification $notification, ?string $driver = null): array
    {
        return $this->resolveCapability(SendsNotifications::class, $driver)->sendToUser($externalId, $notification);
    }

    public function sendToUsers(array $externalIds, Notification $notification, ?string $driver = null): array
    {
        return $this->resolveCapability(SendsNotifications::class, $driver)->sendToUsers($externalIds, $notification);
    }

    public function sendToSegment(string $segment, Notification $notification, ?string $driver = null): array
    {
        return $this->resolveCapability(SendsNotifications::class, $driver)->sendToSegment($segment, $notification);
    }

    // ── TracksEvents Pass-throughs ─────────────────────────────────────

    public function trackEvent(CustomerEvent $event, ?string $driver = null): void
    {
        $this->resolveCapability(TracksEvents::class, $driver)->trackEvent($event);
    }

    public function trackEvents(array $events, ?string $driver = null): void
    {
        $this->resolveCapability(TracksEvents::class, $driver)->trackEvents($events);
    }

    // ── Internal ───────────────────────────────────────────────────────

    protected function capabilityEnabled(string $capability, ?string $driver = null): bool
    {
        $name = $driver ?? $this->getDefaultDriver();

        return (bool) $this->config->get(
            "customer-engagement.drivers.{$name}.capabilities.{$capability}",
            true,
        );
    }

    protected function resolveCapability(string $interface, ?string $driver = null): EngagementDriver
    {
        $capability = self::CAPABILITIES[$interface];

        if (! $this->capabilityEnabled($capability, $driver)) {
            $name = $driver ?? $this->getDefaultDriver();

            $this->container['log']->debug(
                "Engagement capability [{$capability}] is disabled by policy for driver [{$name}]; call ignored."
            );

            return $this->noopDriver ??= new NullDriver;
        }

        $resolved = $this->driver($driver);

        if (! $resolved instanceof $interface) {
            $driverName = $resolved instanceof EngagementDriver ? $resolved->getName() : get_class($resolved);
            $capability = class_basename($interface);

            throw new BadMethodCallException(
                "The [{$driverName}] engagement driver does not support [{$capability}]."
            );
        }

        return $resolved;
    }
}
