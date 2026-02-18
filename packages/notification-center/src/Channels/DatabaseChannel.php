<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Models\Notification;
use malikad778\NotificationCenter\Contracts\Notifiable;

class DatabaseChannel implements ChannelInterface
{
    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult
    {
        // In our architecture, the Notification model is usually created *before* dispatching.
        // However, if we wanted to support direct usage without a pre-existing model,
        // we could create it here.
        // For now, we assume the notification exists and is being processed.
        // If we need to persist it specifically for the "database" channel (e.g. standard Laravel behavior),
        // we would create a record in `notifications` table.
        // But since our `Notification` model *is* that record, we just acknowledge it.
        
        // Optionally, we could update the record to say "available in UI".
        
        return new ChannelResult(
            channel: 'database',
            success: true,
            messageId: null, // The Notification ID itself is the message ID
        );
    }

    public function isAvailable(Notifiable $user): bool
    {
        return true; // Always available for registered users
    }
}
