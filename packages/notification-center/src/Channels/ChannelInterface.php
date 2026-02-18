<?php

namespace malikad778\NotificationCenter\Channels;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Contracts\Notifiable;

interface ChannelInterface
{
    public function send(NotificationPayload $payload, Notifiable $user): ChannelResult;
    
    /**
     * Check if channel is configured and available for user
     */
    public function isAvailable(Notifiable $user): bool;
}
