<?php

namespace App\Features;

use App\Models\User;
use malikad778\NotificationCenter\Models\NotificationPreference;

class ReceiveDatabase
{
    public function resolve(User $user): bool
    {
        $pref = NotificationPreference::where('user_id', $user->id)
            ->where('channel', 'database')
            ->first();

        // Database notifications are usually on by default
        return $pref ? $pref->enabled : true;
    }
}
