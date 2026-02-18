<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Config;

class FallbackResolver
{
    /**
     * Resolve a fallback channel for a given channel.
     *
     * @param Notifiable $user
     * @param string $channel
     * @return string|null
     */
    public function resolve(Notifiable $user, string $channel): ?string
    {
        $fallbacks = Config::get('notification-center.fallback_chain', []);
        
        $chain = $fallbacks[$channel] ?? null;
        return is_array($chain) ? ($chain[0] ?? null) : $chain;
    }
}
