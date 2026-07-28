<?php

use Multek\CustomerEngagement\EngagementManager;
use Multek\CustomerEngagement\NullDriver;

it('resolves the null driver by default', function () {
    $manager = app(EngagementManager::class);

    expect($manager->driver())->toBeInstanceOf(NullDriver::class);
});

it('reports all capabilities for the null driver', function () {
    $manager = app(EngagementManager::class);

    expect($manager->syncsUsers())->toBeTrue()
        ->and($manager->sendsNotifications())->toBeTrue()
        ->and($manager->tracksEvents())->toBeTrue();
});
