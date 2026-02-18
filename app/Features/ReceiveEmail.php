<?php

namespace App\Features;

use App\Models\User;
use malikad778\NotificationCenter\Models\NotificationPreference;

class ReceiveEmail
{
    /**
     * Resolve the feature's initial value.
     */
    public function resolve(User $user): bool
    {
        $pref = NotificationPreference::where('user_id', $user->id)
            ->where('channel', 'email')
            ->first();

        return $pref ? $pref->enabled : true;
    }
}
