<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\Models\NotificationPreference;
use malikad778\NotificationCenter\Contracts\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationRateLimiter
{
    public function __construct(
        protected int $defaultLimit = 10 // Max 10 per hour by default
    ) {}

    public function check(Notifiable $user, string $channel): bool
    {
        $key = "notification_limit:{$user->getAuthIdentifier()}:{$channel}:" . now()->format('YmdH');
        $limit = $this->getLimitForUser($user, $channel);

        $current = Cache::get($key, 0);

        if ($current >= $limit) {
            return false;
        }

        return true;
    }

    public function increment(Notifiable $user, string $channel): void
    {
        $key = "notification_limit:{$user->getAuthIdentifier()}:{$channel}:" . now()->format('YmdH');
        
        // Ensure key exists with expiry (1 hour) if not already present
        Cache::add($key, 0, 3600);
        
        // Increment the count
        Cache::increment($key);
    }

    protected function getLimitForUser(Notifiable $user, string $channel): int
    {
        return Cache::remember("pref_limit:{$user->getAuthIdentifier()}:{$channel}", 3600, function () use ($user, $channel) {
            $pref = NotificationPreference::where('user_id', $user->getAuthIdentifier())
                ->where('channel', $channel)
                ->first();
                
            return $pref?->frequency_limit ?? $this->defaultLimit;
        });
    }
}
