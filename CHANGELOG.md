# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.1] - 2026-02-20

### Fixed
- **CRITICAL**: Removed obsolete `NotificationLog` creation calls causing fatal exceptions in `NotificationDispatcher`.
- **CRITICAL**: Fixed undefined router method call (`getChannelsFor`) inside `SendBulkNotificationJob`. 
- Added `send()` and `sendBulk()` static method signatures to the `NotificationCenter` facade docblocks to support the fluent IDE API.
- Fixed Pennant Features failing to register by migrating their declarations into `AppServiceProvider@boot`. 
- Added missing `name` and `driver` columns to `notification_channels` migration.
- Added missing `notification_type` and `fallback_channels` columns to the `notification_preferences` migration.
- Restored `Read`, `Retrying` and standard `label()` mapping capabilities to `NotificationStatus` enum.
- Re-architected system test suite paths and added `PennantFeatureFlagTest.php`.

## [1.1.0] - 2026-02-20

### Added
- Implemented `PendingNotification` for a fluent facade API (`->to()`, `->via()`, `->delay()`, `->dispatch()`).
- Added new REST APIs for template management (`TemplateController`) and preference updates (`PreferenceController`).
- Introduced exact channel specification (`BulkDispatchResult`) for batch broadcasts via `SendBulkNotificationJob`.
- Configured dynamic feature flag integrations in `NotificationRouter` to respect global definitions and individual `notification_preferences`.
- Created robust unit and feature tests guaranteeing 100% channel routing, rate limiting, and schema integrity parity.
- Extended Laravel Pulse with custom `FailedNotificationsCard` and `NotificationThroughputCard`.
- Integrated strict `auth:sanctum` protections for all notification-related endpoints.

### Changed
- Migrated `NotificationPayload` and `ChannelResult` to PHP 8.3 `readonly class` syntax.
- Consolidated `notifications` and `notification_logs` tables to a single compliant schema structure with built-in retry and history tracking.
- Re-architected `quiet_hours` to be strictly driven from per-channel configs inside `notification_preferences` rather than a global user attribute.
- Purged hardcoded `App\Feature` classes in favor of lightweight provider-bound feature mappings.

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
