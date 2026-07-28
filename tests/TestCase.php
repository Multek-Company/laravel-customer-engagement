<?php

namespace Multek\CustomerEngagement\Tests;

use Multek\CustomerEngagement\CustomerEngagementServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CustomerEngagementServiceProvider::class,
        ];
    }
}
