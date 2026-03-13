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
    title: 'Order Shipped',
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
    tags: ['plan' => 'pro', 'role' => 'admin'],
);

Engagement::createUser($customer);
Engagement::updateUser($customer);
Engagement::deleteUser('user_123');
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
