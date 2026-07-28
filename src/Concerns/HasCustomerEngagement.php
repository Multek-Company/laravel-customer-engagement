<?php

namespace Multek\CustomerEngagement\Concerns;

use Multek\CustomerEngagement\DTOs\Customer;
use Multek\CustomerEngagement\DTOs\CustomerEvent;
use Multek\CustomerEngagement\DTOs\Notification;
use Multek\CustomerEngagement\EngagementManager;
use Multek\CustomerEngagement\Jobs\SyncCustomer;

trait HasCustomerEngagement
{
    public function getEngagementExternalId(): string
    {
        return (string) $this->getKey();
    }

    public function getEngagementAttributes(): array
    {
        $mapping = config('customer-engagement.default_attributes', []);
        $attributes = [];

        foreach ($mapping as $key => $value) {
            $attributes[$key] = $value instanceof \Closure
                ? $value($this)
                : data_get($this, $value);
        }

        return $attributes;
    }

    public function getEngagementLanguage(): ?string
    {
        return null;
    }

    public function getEngagementTimezone(): ?string
    {
        return null;
    }

    public function getEngagementCountry(): ?string
    {
        return null;
    }

    public function toEngagementCustomer(): Customer
    {
        return new Customer(
            externalId: $this->getEngagementExternalId(),
            email: data_get($this, 'email'),
            phone: data_get($this, 'phone'),
            name: data_get($this, 'name'),
            attributes: $this->getEngagementAttributes(),
            language: $this->getEngagementLanguage(),
            timezone: $this->getEngagementTimezone(),
            country: $this->getEngagementCountry(),
        );
    }

    public function syncToEngagement(?string $driver = null): void
    {
        $manager = app(EngagementManager::class);
        $customer = $this->toEngagementCustomer();

        try {
            $manager->updateUser($customer, $driver);
        } catch (\Throwable) {
            $manager->createUser($customer, $driver);
        }
    }

    public function syncToEngagementAsync(?string $driver = null): void
    {
        if (config('customer-engagement.skip_async_when_null', false)) {
            $effective = $driver ?? config('customer-engagement.default', 'null');

            if ($effective === 'null') {
                return;
            }
        }

        SyncCustomer::dispatch($this, $driver);
    }

    public function sendEngagementNotification(Notification $notification, ?string $driver = null): array
    {
        return app(EngagementManager::class)->sendToUser(
            $this->getEngagementExternalId(),
            $notification,
            $driver,
        );
    }

    public function trackEngagementEvent(string $name, array $payload = [], ?\DateTimeInterface $timestamp = null, ?string $driver = null): void
    {
        app(EngagementManager::class)->trackEvent(
            new CustomerEvent(
                externalId: $this->getEngagementExternalId(),
                name: $name,
                payload: $payload,
                timestamp: $timestamp,
            ),
            $driver,
        );
    }

    public function deleteFromEngagement(?string $driver = null): void
    {
        app(EngagementManager::class)->deleteUser(
            $this->getEngagementExternalId(),
            $driver,
        );
    }

    public function routeNotificationForEngagement(): string
    {
        return $this->getEngagementExternalId();
    }
}
