<?php

namespace malikad778\NotificationCenter\Traits;

use malikad778\NotificationCenter\Models\NotificationPreference;
use Illuminate\Database\Eloquent\Relations\HasMany;
use malikad778\NotificationCenter\Services\NotificationDispatcher;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Enums\NotificationPriority;

trait HasNotifications
{
    /**
     * Get the user's notification preferences.
     */
    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * Check if the user is currently in quiet hours.
     */
    public function isInQuietHours(): bool
    {
        if (! $this->quiet_hours_start || ! $this->quiet_hours_end) {
            return false;
        }

        $now = now();
        $start = $now->copy()->setTimeFromTimeString($this->quiet_hours_start);
        $end = $now->copy()->setTimeFromTimeString($this->quiet_hours_end);

        if ($start->greaterThan($end)) {
            // Quiet hours span across midnight (e.g. 22:00 to 07:00)
            return $now->greaterThanOrEqualTo($start) || $now->lessThanOrEqualTo($end);
        }

        return $now->between($start, $end);
    }

    /**
     * Dispatch a notification to this user.
     */
    public function sendNotification(NotificationPayload $payload, NotificationPriority $priority = NotificationPriority::Normal): void
    {
        app(NotificationDispatcher::class)->dispatch($this, $payload, $priority);
    }
}
