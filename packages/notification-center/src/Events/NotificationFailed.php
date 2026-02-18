<?php

namespace malikad778\NotificationCenter\Events;

use malikad778\NotificationCenter\DTOs\ChannelResult;
use malikad778\NotificationCenter\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Notification $notification,
        public ChannelResult $result
    ) {}
}
