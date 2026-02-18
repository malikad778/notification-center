# Laravel Notification Center Service

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://github.com/malikad778/notification-center/actions/workflows/ci.yml/badge.svg)](https://github.com/malikad778/notification-center/actions)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.4-8892bf.svg?style=flat-square)](https://php.net)


**Laravel Notification Center** is a high-performance, enterprise-grade notification microservice built with **Laravel 12** and **PHP 8.4**. It provides a scalable, multi-channel architecture for dispatching real-time notifications via Email, SMS, Push, Slack, and WhatsApp.

![Dashboard Screenshot](dashboard_heo.png)

---

## Architecture

This project is architected for maximum portability and developer productivity:

1.  **Core Package (`packages/notification-center`)**: A decoupled, framework-agnostic-ready domain layer containing all channels, services, and models.
2.  **Service App**: A lightweight Laravel implementation providing REST API endpoints, database orchestration, and monitoring tools.

### Key Technologies
- **PHP 8.4**: Using property hooks, asymmetric visibility, and short-new syntax.
- **Laravel 12**: Leverages `Concurrency::run()` for parallel processing.
- **Laravel Pennant**: For feature-toggle-based routing.
- **Laravel Pulse**: For real-time monitoring and custom metrics.

## Getting Started

1.  **Install Dependencies**
    ```bash
    composer install
    ```

2.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3.  **Database & Registry**
    ```bash
    php artisan migrate
    php artisan db:seed
    ```

4.  **Run Tests**
    ```bash
    php artisan test
    ```

## Documentation

For detailed information on how to use the notification system, refer to the [Package README](packages/notification-center/README.md).

### API Reference
- `GET /api/notifications`: Retrieve user notifications.
- `PATCH /api/notifications/{id}/read`: Mark notification as read.
- `GET /api/notifications/preferences`: Get notification preferences.

## Usage Example

### Dispatching a Multi-Channel Notification

```php
use malikad778\NotificationCenter\Facades\NotificationCenter;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationPriority;

// The system will automatically resolve the best channels (Email, SMS, WhatsApp, etc.)
// based on user preferences and feature flags (Laravel Pennant).
NotificationCenter::dispatch($user, new NotificationPayload(
    title: 'Security Alert',
    body: 'A new login was detected from a new device.',
    actionUrl: 'https://your-app.com/security/sessions'
), NotificationPriority::High);
```

For more advanced configuration, visit the [Package Documentation](packages/notification-center/README.md).

## Running Tests

Visit `/pulse` in your browser to see the real-time notification statistics.

## License

MIT
