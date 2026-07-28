<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | The default engagement driver to use. This should match one of the keys
    | in the 'drivers' array below.
    |
    */

    'default' => env('ENGAGEMENT_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Engagement Drivers
    |--------------------------------------------------------------------------
    |
    | Define your available engagement drivers here. Each driver package
    | (e.g. multek/laravel-onesignal) registers itself via extend().
    |
    | Each driver may declare a capability policy under 'capabilities'
    | (keys: users, notifications, events). Missing keys default to
    | enabled. A disabled capability makes the corresponding checks
    | return false and turns guarded calls into silent no-ops —
    | useful when a plan blocks a feature (e.g. OneSignal Free
    | rejects custom events with 403).
    |
    */

    'drivers' => [
        // 'onesignal' => [
        //     'app_id' => env('ONESIGNAL_APP_ID'),
        //     'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
        //     'capabilities' => [
        //         'users' => true,
        //         'notifications' => true,
        //         'events' => env('ONESIGNAL_TRACK_EVENTS', true),
        //     ],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Attributes
    |--------------------------------------------------------------------------
    |
    | Attributes to sync from your User model to the engagement platform.
    | Keys are the platform attribute names, values are model attributes
    | or closures that receive the model.
    |
    | Example:
    |   'plan' => 'subscription_plan',
    |   'role' => fn($user) => $user->role->name,
    |
    */

    'default_attributes' => [],

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | The queue connection/name to use for async engagement operations.
    |
    */

    'queue' => env('ENGAGEMENT_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Skip Async Dispatch When Null
    |--------------------------------------------------------------------------
    |
    | When enabled, syncToEngagementAsync() will not dispatch the SyncCustomer
    | job if the resolved driver is the null driver — keeping local/test
    | queues quiet. Off by default: the null-driver round-trip keeps the
    | pipeline observable (Horizon) and test dispatch assertions green.
    |
    */

    'skip_async_when_null' => env('ENGAGEMENT_ASYNC_SKIP_NULL', false),

];
