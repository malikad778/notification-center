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
    public function isInQuietHours(?string $channel = null): bool
    {
        $prefs = $channel 
            ? $this->notificationPreferences()->where('channel', $channel)->get()
            : $this->notificationPreferences()->get();

        if ($prefs->isEmpty()) {
            return false;
        }

        $now = now();

        foreach ($prefs as $pref) {
            if (! $pref->quiet_hours_start || ! $pref->quiet_hours_end) {
                continue;
            }

            $start = $now->copy()->setTimeFromTimeString($pref->quiet_hours_start);
            $end = $now->copy()->setTimeFromTimeString($pref->quiet_hours_end);

            if ($start->greaterThan($end)) {
                if ($now->greaterThanOrEqualTo($start) || $now->lessThanOrEqualTo($end)) {
                    return true;
                }
            } else {
                if ($now->between($start, $end)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Dispatch a notification to this user.
     */
    public function sendNotification(NotificationPayload $payload, NotificationPriority $priority = NotificationPriority::Normal): void
    {
        app(NotificationDispatcher::class)->dispatch($this, $payload, $priority);
    }
}
