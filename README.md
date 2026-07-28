# Laravel Customer Engagement

A unified customer engagement platform for Laravel. Provides contracts, DTOs, and a driver-based manager for push notifications, user sync, and event tracking across multiple providers.

## Features

- **Driver-Based Architecture** - Swap providers (OneSignal, Braze, Firebase, etc.) without changing application code
- **Capability Contracts** - `SyncsUsers`, `SendsNotifications`, `TracksEvents` interfaces for fine-grained driver capabilities
- **Laravel Notification Channel** - `EngagementChannel` works with Laravel's native notification system
- **DTOs** - `Customer`, `Notification`, and `CustomerEvent` data transfer objects for type-safe interactions
- **Model Trait** - `HasCustomerEngagement` for syncing Eloquent models
- **Async Jobs** - Queue-based customer sync with `SyncCustomer`
- **Events** - `NotificationSent` and `NotificationFailed` events for observability
- **Null Driver** - Built-in null driver for testing and environments without a provider

## Requirements

- PHP 8.2+
- Laravel 11.0+

## Installation

```bash
composer require multek/laravel-customer-engagement
```

Publish the config file:

```bash
php artisan vendor:publish --tag=customer-engagement-config
```

## Available Drivers

| Driver | Package | Capabilities |
|--------|---------|-------------|
| OneSignal | [`multek/laravel-onesignal`](https://github.com/Multek-Company/laravel-onesignal) | Notifications, User Sync, Event Tracking |
| Null | Built-in | None (testing/fallback) |

## Usage

### Configuration

```php
// config/customer-engagement.php
'default' => env('ENGAGEMENT_DRIVER', 'onesignal'),
```

### Sending Notifications

```php
use Multek\CustomerEngagement\Facades\Engagement;
use Multek\CustomerEngagement\DTOs\Notification;

$notification = new Notification(
    heading: 'Order Shipped',
    body: 'Your order #456 has been shipped.',
    data: ['order_id' => 456],
);

// Send to a single user
Engagement::sendToUser('user_123', $notification);

// Send to multiple users
Engagement::sendToUsers(['user_1', 'user_2'], $notification);

// Send to a segment
Engagement::sendToSegment('Active Users', $notification);
```

### User Sync

```php
use Multek\CustomerEngagement\DTOs\Customer;

$customer = new Customer(
    externalId: 'user_123',
    attributes: ['plan' => 'pro', 'role' => 'admin'], // custom segmentation tags
    language: 'pt',                                   // ISO 639-1
    timezone: 'America/Sao_Paulo',                    // IANA timezone id
    country: 'BR',                                    // ISO 3166-1 alpha-2
);

Engagement::createUser($customer);
Engagement::updateUser($customer);
Engagement::deleteUser('user_123');
```

`language`, `timezone`, and `country` are **native profile properties**, kept separate
from `attributes` (custom segmentation tags) on purpose: platforms often cap custom tags
per plan — OneSignal allows 2 data tags per user on Free, 10 on Growth, and 100 on
Professional — while native profile properties are free on every plan and power built-in
features like localized content and per-timezone sends. Keep `attributes` for custom
segmentation only and put profile data in the dedicated fields; drivers map them to their
native equivalents (drivers without a native slot may ignore them).

On models using the `HasCustomerEngagement` trait, override the corresponding getters
(all default to `null`) and `toEngagementCustomer()` passes them through:

```php
class User extends Authenticatable
{
    use HasCustomerEngagement;

    public function getEngagementLanguage(): ?string
    {
        return $this->locale;
    }

    public function getEngagementTimezone(): ?string
    {
        return $this->timezone;
    }

    public function getEngagementCountry(): ?string
    {
        return $this->country_code;
    }
}
```

### Event Tracking

```php
use Multek\CustomerEngagement\DTOs\CustomerEvent;

$event = new CustomerEvent(
    externalId: 'user_123',
    name: 'purchase',
    payload: ['amount' => 99.90, 'product' => 'Pro Plan'],
);

Engagement::trackEvent($event);
```

### Capability Checks

```php
// Check what the current driver supports
Engagement::syncsUsers();          // bool
Engagement::sendsNotifications();  // bool
Engagement::tracksEvents();        // bool

// Check a specific driver
Engagement::syncsUsers('onesignal'); // bool
```

### Capability Policy (per driver)

Capability checks normally reflect what the driver *implements*. You can additionally
restrict what a driver is *allowed* to do via config — useful when a provider plan blocks
a feature (e.g. OneSignal Free rejects custom events with `403`), or when a driver should
only handle part of the pipeline:

```php
// config/customer-engagement.php
'drivers' => [
    'onesignal' => [
        'app_id' => env('ONESIGNAL_APP_ID'),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
        'capabilities' => [
            'users' => true,
            'notifications' => true,
            'events' => false, // OneSignal Free: custom events are blocked
        ],
    ],
],
```

Missing keys default to **enabled**, so existing configs keep working. When a capability
is disabled by policy:

- `syncsUsers()` / `sendsNotifications()` / `tracksEvents()` return `false`, regardless
  of the driver's contracts.
- Guarded manager calls (`trackEvent()`, `sendToUser()`, `createUser()`, …) become
  **silent no-ops** (array-returning methods return `[]`), with a debug-level log line.
  No exceptions, no failed jobs — upgrade your plan later and flip the flag back, no
  deploy needed.

### Skipping Async Dispatch on the Null Driver

In local/test environments the null driver runs the whole pipeline as a no-op — including
the `SyncCustomer` queue round-trip from `syncToEngagementAsync()`. That is by design
(same code path everywhere, observable in Horizon), but if you prefer a quiet local
queue you can opt out of the dispatch entirely:

```bash
ENGAGEMENT_ASYNC_SKIP_NULL=true
```

```php
// config/customer-engagement.php
'skip_async_when_null' => env('ENGAGEMENT_ASYNC_SKIP_NULL', false),
```

When enabled, `syncToEngagementAsync()` returns without dispatching `SyncCustomer` if the
effective driver (the argument or the configured default) is the null driver. Default is
`false`: enabling it in a test environment changes dispatch semantics — test suites that
assert `SyncCustomer` was dispatched while running on the null driver rely on the default
behavior.

### Using a Specific Driver

```php
// Override the default driver for a single call
Engagement::sendToUser('user_123', $notification, driver: 'onesignal');
```

## Creating a Custom Driver

Implement the contracts your provider supports:

```php
use Multek\CustomerEngagement\Contracts\EngagementDriver;
use Multek\CustomerEngagement\Contracts\SendsNotifications;
use Multek\CustomerEngagement\Contracts\SyncsUsers;

class BrazeDriver implements EngagementDriver, SendsNotifications, SyncsUsers
{
    public function getName(): string
    {
        return 'braze';
    }

    // Implement SendsNotifications methods...
    // Implement SyncsUsers methods...
}
```

Register it in your service provider:

```php
use Multek\CustomerEngagement\EngagementManager;

public function boot(): void
{
    $this->app->make(EngagementManager::class)->extend('braze', function () {
        return new BrazeDriver(config('services.braze'));
    });
}
```

## Testing

```bash
composer test
```

## License

MIT
