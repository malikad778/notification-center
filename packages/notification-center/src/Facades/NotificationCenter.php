<?php

namespace malikad778\NotificationCenter\Facades;

use Illuminate\Support\Facades\Facade;
use malikad778\NotificationCenter\Services\NotificationDispatcher;

/**
 * @method static array dispatch(\malikad778\NotificationCenter\Contracts\Notifiable $user, \malikad778\NotificationCenter\DTOs\NotificationPayload $payload, \malikad778\NotificationCenter\Enums\NotificationPriority $priority = \malikad778\NotificationCenter\Enums\NotificationPriority::Normal, array $overrideChannels = [])
 * @method static \malikad778\NotificationCenter\Services\PendingNotification send(\malikad778\NotificationCenter\Contracts\Notifiable $to, \malikad778\NotificationCenter\DTOs\NotificationPayload $notification)
 * @method static void sendBulk(array $users, \malikad778\NotificationCenter\DTOs\NotificationPayload $payload, array $channels = [])
 * 
 * @see \malikad778\NotificationCenter\Services\NotificationDispatcher
 */
class NotificationCenter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return NotificationDispatcher::class;
    }
}
