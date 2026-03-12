<?php

namespace Multek\CustomerEngagement;

use Illuminate\Support\ServiceProvider;
use Multek\CustomerEngagement\Channels\EngagementChannel;

class CustomerEngagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/customer-engagement.php', 'customer-engagement');

        $this->app->singleton(EngagementManager::class, function ($app) {
            return new EngagementManager($app);
        });

        $this->app->singleton(EngagementChannel::class, function ($app) {
            return new EngagementChannel($app->make(EngagementManager::class));
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/customer-engagement.php' => config_path('customer-engagement.php'),
        ], 'customer-engagement-config');
    }
}
