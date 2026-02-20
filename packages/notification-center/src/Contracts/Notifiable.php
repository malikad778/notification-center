<?php

namespace malikad778\NotificationCenter\Contracts;

/**
 * Interface Notifiable
 * 
 * Defines the contract for models that can receive notifications.
 */
interface Notifiable
{
    /**
     * Get the ID of the notifiable entity.
     */
    public function getAuthIdentifier();

    /**
     * Check if the notifiable entity is currently in quiet hours.
     */
    public function isInQuietHours(?string $channel = null): bool;

    /**
     * Get the primary identifier for a specific channel (e.g. email, phone_number, fcm_token).
     */
    public function getNotificationIdentifier(string $channel): ?string;
}
