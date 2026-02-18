# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-02-18

### Added
- Initial release of the Laravel Notification Center microservice.
- Integrated multi-channel support: Email, SMS (Twilio), WhatsApp, Slack, Database, and Push (FCM).
- Parallel dispatching using Laravel 12 Concurrency.
- Smart routing with Laravel Pennant integration.
- Real-time monitoring with custom Laravel Pulse cards.
- Decoupled `malikad778/notification-center` package for portability.
- Fallback chain mechanisms for reliable message delivery.
- Rate limiting and notification grouping services.
- OpenAPI/Swagger documentation for the Service API.
