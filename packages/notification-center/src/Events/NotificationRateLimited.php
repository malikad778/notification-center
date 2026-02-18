<?php

namespace malikad778\NotificationCenter\Events;

use malikad778\NotificationCenter\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationRateLimited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Notification $notification,
        public User $user,
        public string $channel
    ) {}
}
