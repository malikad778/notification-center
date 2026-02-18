# Laravel Notification Center

[![GitHub License](https://img.shields.io/github/license/malikad778/notification-center?style=flat-square)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.4-8892bf.svg?style=flat-square)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-12.x-red.svg?style=flat-square)](https://laravel.com)

A premium, feature-rich notification engine for Laravel 12+, engineered for speed, reliability, and observability.

## Key Features

- **Multi-Channel Support**: Native integration for Email, SMS (Twilio), Database, Slack (Webhooks), WhatsApp, and FCM Push Notifications.
- **Parallel Dispatching**: Leverages Laravel 12's `Concurrency::run()` to sends notifications through multiple channels simultaneously without blocking.
- **Smart Routing**: Powerful integration with **Laravel Pennant** for dynamic, feature-toggle-based channel resolution.
- **Resilience & Failover**: Configurable fallback chains ensure delivery even if a primary channel fails.
- **Smart Quiet Hours**: Respects user-defined quiet hours with priority-based urgent bypass.
- **Rate Limiting**: Intelligent per-user/per-channel hourly limits with high-performance caching.
- **Smart Grouping**: Consolidates similar notifications to prevent user fatigue.
- **Real-time Observability**: Custom **Laravel Pulse** cards for live performance monitoring.
- **Template Engine**: Centralized management for dynamic, placeholder-based notification templates.

## Installation

```bash
composer require malikad778/notification-center
```

Publish the configuration and migrations:

```bash
php artisan vendor:publish --tag="notification-center-config"
php artisan vendor:publish --tag="notification-center-migrations"
php artisan migrate
```

## Basic Usage

### Using the Facade

```php
use malikad778\NotificationCenter\Facades\NotificationCenter;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationPriority;

NotificationCenter::dispatch($user, new NotificationPayload(
    title: 'Welcome!',
    body: 'Thanks for joining our platform.',
    actionUrl: 'https://example.com/welcome'
), NotificationPriority::Normal);
```

### Using the Trait

Add `HasNotifications` to your `User` model:

```php
use malikad778\NotificationCenter\Traits\HasNotifications;

class User extends Authenticatable
{
    use HasNotifications;
}

// Then dispatch directly
$user->sendNotification(new NotificationPayload(...));
```

## Advanced Features

### Fallback Chains

Configure fallback behavior in `config/notification-center.php`:

```php
'fallback_chain' => [
    'whatsapp' => ['sms', 'mail'],
    'push' => ['mail', 'database'],
],
```

### Templates

```php
use malikad778\NotificationCenter\Services\NotificationTemplateService;

app(NotificationTemplateService::class)->send($user, 'welome_email', [
    'name' => 'John Doe',
    'plan' => 'Pro'
]);
```

### Pulse Monitoring

Add the `NotificationStats` card to your Pulse dashboard:

```blade
<livewire:pulse.notification-stats cols="6" />
```

## Configuration

The package behavior can be customized in `config/notification-center.php`. You can define custom channel classes, rate limits, and quiet hour defaults.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
