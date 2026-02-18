<?php

namespace malikad778\NotificationCenter\Facades;

use Illuminate\Support\Facades\Facade;
use malikad778\NotificationCenter\Services\NotificationDispatcher;

/**
 * @method static array dispatch(\App\Models\User $user, \malikad778\NotificationCenter\DTOs\NotificationPayload $payload, \malikad778\NotificationCenter\Enums\NotificationPriority $priority = \malikad778\NotificationCenter\Enums\NotificationPriority::Normal)
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
