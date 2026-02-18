<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Events\NotificationPushed;
use malikad778\NotificationCenter\Contracts\Notifiable;

class BroadcastChannel implements ChannelInterface
{
    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult
    {
        event(new NotificationPushed($user, $payload));

        return new ChannelResult(
            channel: 'broadcast',
            success: true,
            messageId: 'broadcast_' . uniqid()
        );
    }

    public function isAvailable(Notifiable $user): bool
    {
        return true; // Always available
    }
}
