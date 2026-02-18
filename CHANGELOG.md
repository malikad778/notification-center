# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-02-18

### Added
- Initial release of the Laravel Notification Center microservice.
- Integrated multi-channel support: Email, SMS (Twilio), WhatsApp, Slack, Database, and Push (FCM).
- Parallel dispatching using Laravel 12 Concurrency.
- CI Compatibility: Synchronized `NotificationChannel` enum with implementation classes, resolving naming mismatches ("mail" vs "email") and fixing metric logging failures.
- Broadcast Support: Implemented the missing `BroadcastChannel` to send real-time client-side notifications.
- Pulse Precision: Guaranteed metric capture in background jobs by firing events directly within the job cycle.
- Local Dashboard Real-time: Implemented a fallback in `NotificationStats.php` to bypass aggregation delays locally.
- Smart routing with Laravel Pennant integration.
- Real-time monitoring with custom Laravel Pulse cards.
- Decoupled `malikad778/notification-center` package for portability.
- Fallback chain mechanisms for reliable message delivery.
- Rate limiting and notification grouping services.
- OpenAPI/Swagger documentation for the Service API.
- [NEW] Comprehensive test suite (Pest) for all channels and dispatcher logic.
- [NEW] Pulse Dashboard integration with custom `NotificationPulseRecorder`.
- [NEW] GitHub Actions CI workflow for automated testing.

### Changed
- Refactored entire codebase to `malikad778` namespace.
- Decoupled system from `App\Models\User` via `Notifiable` contract.
- Optimized `NotificationDispatcher` to fire events in parent process for reliable Pulse metrics.
- Enhanced `SendNotificationJob` with Pulse event triggers for background processing.
- Cleaned up diagnostic logging and removed testing artifacts for v1.0.0 release.
