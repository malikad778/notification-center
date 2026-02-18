<?php

namespace malikad778\NotificationCenter\Services;

use malikad778\NotificationCenter\DTOs\NotificationPayload;
use malikad778\NotificationCenter\Models\NotificationGroup;
use malikad778\NotificationCenter\Contracts\Notifiable;

class NotificationGrouper
{
    public function handle(Notifiable $user, NotificationPayload $payload): ?int
    {
        if (!$payload->groupKey) {
            return null;
        }

        $group = NotificationGroup::firstOrCreate(
            [
                'user_id' => $user->id,
                'group_key' => $payload->groupKey,
            ],
            [
                'group_label' => $payload->groupLabel ?? $payload->title,
                'count' => 0,
            ]
        );

        $group->increment('count');
        $group->touch(); // Update updated_at

        return $group->id;
    }
}
