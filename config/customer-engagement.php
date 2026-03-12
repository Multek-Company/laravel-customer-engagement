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
    */

    'drivers' => [
        // 'onesignal' => [
        //     'app_id' => env('ONESIGNAL_APP_ID'),
        //     'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
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

];
