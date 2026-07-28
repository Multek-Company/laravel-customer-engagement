<?php

use Multek\CustomerEngagement\Contracts\EngagementDriver;
use Multek\CustomerEngagement\Contracts\SendsNotifications;
use Multek\CustomerEngagement\Contracts\SyncsUsers;
use Multek\CustomerEngagement\Contracts\TracksEvents;
use Multek\CustomerEngagement\DTOs\Customer;
use Multek\CustomerEngagement\DTOs\CustomerEvent;
use Multek\CustomerEngagement\DTOs\Notification;
use Multek\CustomerEngagement\EngagementManager;

class SpyDriver implements EngagementDriver, SendsNotifications, SyncsUsers, TracksEvents
{
    public array $calls = [];

    public function getName(): string
    {
        return 'spy';
    }

    public function getUser(string $externalId): array
    {
        $this->calls[] = 'getUser';

        return ['id' => $externalId];
    }

    public function createUser(Customer $customer): array
    {
        $this->calls[] = 'createUser';

        return ['created' => true];
    }

    public function updateUser(Customer $customer): array
    {
        $this->calls[] = 'updateUser';

        return ['updated' => true];
    }

    public function deleteUser(string $externalId): void
    {
        $this->calls[] = 'deleteUser';
    }

    public function sendToUser(string $externalId, Notification $notification): array
    {
        $this->calls[] = 'sendToUser';

        return ['sent' => true];
    }

    public function sendToUsers(array $externalIds, Notification $notification): array
    {
        $this->calls[] = 'sendToUsers';

        return ['sent' => true];
    }

    public function sendToSegment(string $segment, Notification $notification): array
    {
        $this->calls[] = 'sendToSegment';

        return ['sent' => true];
    }

    public function trackEvent(CustomerEvent $event): void
    {
        $this->calls[] = 'trackEvent';
    }

    public function trackEvents(array $events): void
    {
        $this->calls[] = 'trackEvents';
    }
}

function spyManager(array $capabilities = [], ?SpyDriver $spy = null): EngagementManager
{
    $spy ??= new SpyDriver;

    config()->set('customer-engagement.default', 'spy');

    if ($capabilities !== []) {
        config()->set('customer-engagement.drivers.spy.capabilities', $capabilities);
    }

    $manager = app(EngagementManager::class);
    $manager->extend('spy', fn () => $spy);

    return $manager;
}

it('keeps capabilities enabled when no policy is configured', function () {
    $manager = spyManager();

    expect($manager->syncsUsers())->toBeTrue()
        ->and($manager->sendsNotifications())->toBeTrue()
        ->and($manager->tracksEvents())->toBeTrue();
});

it('keeps missing capability keys enabled', function () {
    $manager = spyManager(['events' => false]);

    expect($manager->syncsUsers())->toBeTrue()
        ->and($manager->sendsNotifications())->toBeTrue();
});

it('reports a disabled capability as false regardless of driver contracts', function () {
    $manager = spyManager(['events' => false]);

    expect($manager->tracksEvents())->toBeFalse();
});

it('turns guarded event calls into silent no-ops when events are disabled', function () {
    $spy = new SpyDriver;
    $manager = spyManager(['events' => false], $spy);

    $manager->trackEvent(new CustomerEvent(externalId: 'u1', name: 'purchase'));
    $manager->trackEvents([new CustomerEvent(externalId: 'u1', name: 'purchase')]);

    expect($spy->calls)->toBeEmpty();
});

it('turns guarded user calls into no-ops returning empty arrays when users are disabled', function () {
    $spy = new SpyDriver;
    $manager = spyManager(['users' => false], $spy);

    expect($manager->getUser('u1'))->toBe([])
        ->and($manager->createUser(new Customer(externalId: 'u1')))->toBe([])
        ->and($manager->updateUser(new Customer(externalId: 'u1')))->toBe([]);

    $manager->deleteUser('u1');

    expect($spy->calls)->toBeEmpty();
});

it('turns guarded notification calls into no-ops when notifications are disabled', function () {
    $spy = new SpyDriver;
    $manager = spyManager(['notifications' => false], $spy);

    $notification = new Notification(body: 'There');

    expect($manager->sendToUser('u1', $notification))->toBe([])
        ->and($manager->sendToUsers(['u1'], $notification))->toBe([])
        ->and($manager->sendToSegment('all', $notification))->toBe([]);

    expect($spy->calls)->toBeEmpty();
});

it('still passes calls through when the capability is enabled explicitly', function () {
    $spy = new SpyDriver;
    $manager = spyManager(['events' => true], $spy);

    $manager->trackEvent(new CustomerEvent(externalId: 'u1', name: 'purchase'));

    expect($spy->calls)->toBe(['trackEvent']);
});

it('applies the policy to an explicitly named driver argument', function () {
    $spy = new SpyDriver;
    config()->set('customer-engagement.drivers.spy.capabilities', ['events' => false]);

    $manager = app(EngagementManager::class);
    $manager->extend('spy', fn () => $spy);

    expect($manager->tracksEvents('spy'))->toBeFalse();

    $manager->trackEvent(new CustomerEvent(externalId: 'u1', name: 'purchase'), 'spy');

    expect($spy->calls)->toBeEmpty();
});

it('does not throw for a policy-disabled capability the driver does not implement', function () {
    config()->set('customer-engagement.default', 'null');
    config()->set('customer-engagement.drivers.null.capabilities', ['events' => false]);

    $manager = app(EngagementManager::class);

    $manager->trackEvent(new CustomerEvent(externalId: 'u1', name: 'purchase'));

    expect($manager->tracksEvents())->toBeFalse();
});
